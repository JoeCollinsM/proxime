<?php

namespace App\PaymentMethods;

use App\Models\Currency;
use App\Events\OrderPaymentConfirmed;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PayPal extends PaymentMethod
{
    public function process(\App\PaymentMethod $dbMethod, Payment $payment, User $user)
    {
        $currency = Currency::getDefaultCurrency();
        $paypalUrl = config('app.debug') ? 'https://www.sandbox.paypal.com/cgi-bin/webscr' : 'https://secure.paypal.com/cgi-bin/webscr';
        $params = [
            'cmd' => '_xclick',
            'business' => $dbMethod->cred1,
            'cbt' => config('app.name'),
            'currency_code' => $currency->code,
            'quantity' => 1,
            'item_name' => 'Order ' . optional($payment->order)->track,
            'custom' => $payment->track,
            'amount' => $payment->gross_amount,
            'return' => route('payment.success', ['type' => 'order', 'ref' => $payment->track]),
            'cancel_return' => route('payment.failed', ['type' => 'order', 'ref' => $payment->track]),
            'notify_url' => route('payment.ipn', ['type' => 'order', 'ref' => $payment->track]),
        ];
        return redirect($paypalUrl . '?' . http_build_query($params));
    }

    public function ipn(Request $request, \App\PaymentMethod $dbMethod, Payment $payment)
    {
        $raw_post_data = $request->getContent();
        $raw_post_array = explode('&', $raw_post_data);
        $myPost = array();
        foreach ($raw_post_array as $keyval) {
            $keyval = explode('=', $keyval);
            if (count($keyval) == 2)
                $myPost[$keyval[0]] = urldecode($keyval[1]);
        }

        $req = 'cmd=_notify-validate';
        if (function_exists('get_magic_quotes_gpc')) {
            $get_magic_quotes_exists = true;
        }
        foreach ($myPost as $key => $value) {
            if ($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
                $value = urlencode(stripslashes($value));
            } else {
                $value = urlencode($value);
            }
            $req .= "&$key=$value";
        }


        $paypalURL = config('app.env') != 'production' ? "https://www.sandbox.paypal.com/cgi-bin/webscr" : "https://secure.paypal.com/cgi-bin/webscr";
        $ch = curl_init($paypalURL);
        if ($ch == FALSE) {
            return FALSE;
        }
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
        curl_setopt($ch, CURLOPT_SSLVERSION, 6);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);

        // Set TCP timeout to 30 seconds
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close', 'User-Agent: ItechSoftSolution'));
        $res = curl_exec($ch);
        $tokens = explode("\r\n\r\n", trim($res));
        $res = trim(end($tokens));

        if (strcmp($res, "VERIFIED") == 0 || strcasecmp($res, "VERIFIED") == 0) {
            $currency = Currency::getDefaultCurrency();
            $receiver_email = $request->receiver_email;
            $mc_currency = $request->mc_currency;
            $mc_gross = $request->mc_gross;
            $track = $request->custom;
            if ($receiver_email == $dbMethod->cred1 && $payment->status == 0) {
                DB::beginTransaction();
                try {
                    if ($mc_currency != $currency->code || $mc_gross < $payment->gross_amount) {
                        $r = $payment->update([
                            'status' => 2,
                            'transaction_id' => $request->txn_id,
                            'note' => ($mc_currency != $currency->code ? 'Currency not matched, paypal currency ' . $mc_currency . ', system currency ' . $currency->code : '') . ($mc_gross < $payment->gross_amount ? ' amount not matched, paypal amount ' . $mc_gross . ', order amount ' . $payment->gross_amount : '')
                        ]);
                        if (!$r) throw new \Exception('Payment not updated. TXN ID ' . $request->txn_id);
                        $r2 = $payment->order->update([
                            'status' => -1
                        ]);
                        if (!$r2) {
                            $payment->order->addNote(($mc_currency != $currency->code ? 'Currency not matched, paypal currency ' . $mc_currency . ', system currency ' . $currency->code : '') . ($mc_gross < $payment->gross_amount ? ' amount not matched, paypal amount ' . $mc_gross . ', order amount ' . $payment->gross_amount : ''));
                            throw new \Exception('Order not updated. Order ID ' . $payment->order_id);
                        }
                    } else {
                        $r = $payment->update([
                            'status' => 1,
                            'transaction_id' => $request->txn_id
                        ]);
                        if (!$r) throw new \Exception('Payment not updated. TXN ID ' . $request->txn_id);
                        $r2 = $payment->order->update([
                            'status' => 0
                        ]);
                        if (!$r2) throw new \Exception('Order not updated. Order ID ' . $payment->order_id);
                        $payment->order->addNote('Payment received from PayPal. TXN ID ' . $request->txn_id);
                        event(new OrderPaymentConfirmed($payment));
                    }
                } catch (\Exception $exception) {
                    DB::rollBack();
                }
                DB::commit();
            }
        }
    }
}
