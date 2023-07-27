<?php

namespace App\PaymentMethods;

use App\Events\OrderPaymentConfirmed;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CashOnDelivery extends PaymentMethod
{
    public function process(\App\PaymentMethod $dbMethod, Payment $payment, User $user)
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
            $payment->order->addNote('Payment request resolved by Cash On Delivery');
            event(new OrderPaymentConfirmed($payment));
        } catch (\Exception $exception) {
            DB::rollBack();
            return redirect()->route('payment.failed', ['type' => 'order', 'ref' => $payment->track])->withErrors($exception->getMessage());
        }
        DB::commit();
        return redirect()->route('payment.success', ['type' => 'order', 'ref' => $payment->track])->withSuccess('Order Placed Successfully');
    }
}
