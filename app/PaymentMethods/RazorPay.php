<?php

namespace App\PaymentMethods;

use App\Models\Currency;
use App\Events\OrderPaymentConfirmed;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Razorpay\Api\Api;
use Razorpay\Api\Order;
use Razorpay\Api\Utility;

class RazorPay extends PaymentMethod
{
    public function process(\App\PaymentMethod $dbMethod, Payment $payment, User $user)
    {
        $currency = Currency::getDefaultCurrency();
        $api = new Api($dbMethod->cred1, $dbMethod->cred2);
        /* @var Order $order */
        $order = $api->order->create([
            'receipt' => 'order_' . $payment->order_id,
            'amount' => ($payment->gross_amount * 100),
            'currency' => $currency->code
        ]);
        $payment->metas()->updateOrCreate(['name' => 'razorpay_order_id'], ['content' => $order['id']]);
        return view('payment.razor-pay', compact('currency', 'dbMethod', 'payment', 'user', 'order'));
    }

    public function ipn(Request $request, \App\PaymentMethod $dbMethod, Payment $payment)
    {
        $currency = Currency::getDefaultCurrency();
        $razorpay_order_id_stored = $payment->metas()->where('name', 'razorpay_order_id')->first();
        if (!$razorpay_order_id_stored) return redirect()->route('payment.failed', ['type' => 'order', 'ref' => $payment->track]);
        $razorpay_order_id_stored = $razorpay_order_id_stored->content;
        $api = new Api($dbMethod->cred1, $dbMethod->cred2);
        try {
            $api->utility->verifyPaymentSignature([
                'razorpay_signature' => $request->razorpay_signature,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_order_id' => $razorpay_order_id_stored
            ]);
        } catch (\Exception $exception) {
            return redirect()->route('payment.failed', ['type' => 'order', 'ref' => $payment->track])->withErrors($exception->getMessage());
        }

        try {
            $order = $api->order->fetch($razorpay_order_id_stored);
            if ($order['status'] != 'paid') throw new \Exception('Payment status not paid');
            if ($order['amount_paid'] < ($payment->gross_amount * 100)) throw new \Exception('Paid less then main amount');
            if (strtolower($order['currency']) != strtolower($currency->code)) throw new \Exception('Currency not matched');
        } catch (\Exception $exception) {
            return redirect()->route('payment.failed', ['type' => 'order', 'ref' => $payment->track])->withErrors($exception->getMessage());
        }
        DB::beginTransaction();
        try {
            $payment->update([
                'status' => 1,
                'transaction_id' => $request->razorpay_payment_id
            ]);
            $payment->order->update([
                'status' => 0
            ]);
            $payment->order->addNote('Payment received via RazorPay. TXN ID ' . $request->razorpay_payment_id);
            event(new OrderPaymentConfirmed($payment));
        } catch (\Exception $exception) {
            DB::rollBack();
            return redirect()->route('payment.failed', ['type' => 'order', 'ref' => $payment->track])->withErrors($exception->getMessage());
        }
        DB::commit();
        return redirect()->route('payment.success', ['type' => 'order', 'ref' => $payment->track])->withSuccess('Payment successfully completed');
    }
}
