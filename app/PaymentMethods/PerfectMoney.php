<?php
namespace App\PaymentMethods;

use App\Models\Currency;
use App\Events\OrderPaymentConfirmed;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PerfectMoney extends PaymentMethod
{
    function process(\App\PaymentMethod $dbMethod, Payment $payment, User $user)
    {
        $currency = Currency::getDefaultCurrency();
        return view('payment.perfect-money', compact('currency', 'dbMethod', 'payment', 'user'));
    }

    public function ipn(Request $request, \App\PaymentMethod $dbMethod, Payment $payment)
    {
        $passphrase = strtoupper(md5($dbMethod->cred2));

        $string =
            $request->post('PAYMENT_ID') . ':' . $request->post('PAYEE_ACCOUNT') . ':' .
            $request->post('PAYMENT_AMOUNT') . ':' . $request->post('PAYMENT_UNITS') . ':' .
            $request->post('PAYMENT_BATCH_NUM') . ':' .
            $request->post('PAYER_ACCOUNT') . ':' . $passphrase . ':' .
            $request->post('TIMESTAMPGMT');

        $hash = strtoupper(md5($string));
        $hash2 = $request->post('V2_HASH');

        if ($hash == $hash2) {

            $amo = $request->post('PAYMENT_AMOUNT');
            $unit = $request->post('PAYMENT_UNITS');
            $track = $request->post('PAYMENT_ID');

            if ($request->post('PAYEE_ACCOUNT') == $dbMethod->cred1 && $payment->status == 0) {
                DB::beginTransaction();
                try {
                    $payment->update([
                        'status' => 1,
                        'transaction_id' => $track
                    ]);
                    $payment->order->update([
                        'status' => 0
                    ]);
                    $payment->order->addNote('Payment received via Perfect Money. TXN ID ' . $track);
                    event(new OrderPaymentConfirmed($payment));
                } catch (\Exception $exception) {
                    DB::rollBack();
                }
                DB::commit();
            }
        }
    }
}
