<?php
namespace App\PaymentMethods;
use App\Models\Currency;
use App\Events\OrderPaymentConfirmed;
use App\Models\Payment;
use App\Models\User;
use App\Http\Controllers\Mpesa\MPESAController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MPesa extends PaymentMethod
{
    function process(\App\PaymentMethod $dbMethod, Payment $payment, User $user) {

        $currency = Currency::getDefaultCurrency();
        return view('payment.mpesa', compact('currency', 'dbMethod', 'payment', 'user'));
    }

    function ipn(Request $request, \App\PaymentMethod $dbMethod, Payment $payment)
    {
        DB::beginTransaction();
        try {
            $payment->update([
                'status' => 1,
                'transaction_id' => $payment->track
            ]);
            $payment->order->update([
                'status' => 0
            ]);
            $payment->order->addNote('Payment request resolved by MPESA');
            event(new OrderPaymentConfirmed($payment));
        } catch (\Exception $exception) {
            DB::rollBack();
            return redirect()->route('payment.failed', ['type' => 'order', 'ref' => $payment->track])->withErrors($exception->getMessage());
        }
        DB::commit();
        return redirect()->route('payment.success', ['type' => 'order', 'ref' => $payment->track])->withSuccess('Order Placed Successfully');

    }
}
