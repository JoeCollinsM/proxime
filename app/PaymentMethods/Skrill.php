<?php
namespace App\PaymentMethods;

use App\Models\Currency;
use App\Events\OrderPaymentConfirmed;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Skrill extends PaymentMethod
{
    public function process(\App\PaymentMethod $dbMethod, Payment $payment, User $user)
    {
        $currency = Currency::getDefaultCurrency();
        return view('payment.skrill', compact('currency', 'dbMethod', 'payment', 'user'));
    }

    public function ipn(Request $request, \App\PaymentMethod $dbMethod, Payment $payment)
    {
        $concatFields = $request->post('merchant_id')
            . $request->post('transaction_id')
            . strtoupper(md5($dbMethod->cred2))
            . $request->post('mb_amount')
            . $request->post('mb_currency')
            . $request->post('status');

        if (strtoupper(md5($concatFields)) == $request->post('md5sig') && $request->post('status') == 2 && $request->post('pay_to_email') == $dbMethod->cred1 && $payment->status = 0) {
            DB::beginTransaction();
            try {
                $payment->update([
                    'status' => 1,
                    'transaction_id' => $payment->track
                ]);
                $payment->order->update([
                    'status' => 0
                ]);
                $payment->order->addNote('Payment received via Skrill. TXN ID ' . $payment->track);
                event(new OrderPaymentConfirmed($payment));
            } catch (\Exception $exception) {
                DB::rollBack();
            }
            DB::commit();
        }
    }
}
