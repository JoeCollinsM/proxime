<?php

namespace App\PaymentMethods;

use App\Events\OrderPaymentConfirmed;
use App\Events\TransactionAdded;
use App\Models\Payment;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class Wallet extends PaymentMethod
{
    public function process(\App\PaymentMethod $dbMethod, Payment $payment, User $user)
    {
        
        DB::beginTransaction();
        try {
            if ($payment->gross_amount > $user->balance) throw new \Exception('Not enough balance');
            $r = $user->update([
                'balance' => ($user->balance - $payment->gross_amount)
            ]);
            if (!$r) throw new \Exception('Unable to update user balance');
            /* @var Transaction $transaction */
            $transaction = $user->transactions()->create([
                'track' => Transaction::generateTrack(),
                'title' => sprintf('Order Payment. Order ID #%s', $payment->order->id),
                'ref_type' => get_class($payment),
                'ref_id' => $payment->id,
                'type' => '-',
                'amount' => $payment->gross_amount,
                'matter' => 'order_payment'
            ]);
            if (!$transaction) throw new \Exception('Unable to add new transaction');
            event(new TransactionAdded($transaction));
            $r2 = $payment->update([
                'status' => 1,
                'transaction_id' => $transaction->id
            ]);
            if (!$r2) throw new \Exception('Unable to update order payment');
            $r3 = $payment->order->update([
                'status' => 0
            ]);
            if (!$r3) throw new \Exception('Unable to update order');
            $payment->order->addNote('Payment request resolved by Wallet');
            event(new OrderPaymentConfirmed($payment));
        } catch (\Exception $exception) {
            DB::rollBack();
            return redirect()->route('payment.failed', ['type' => 'order', 'ref' => $payment->track])->withErrors($exception->getMessage());
        }
        DB::commit();
        return redirect()->route('payment.success', ['type' => 'order', 'ref' => $payment->track])->withSuccess('Order Placed Successfully');
    }
}
