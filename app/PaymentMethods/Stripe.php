<?php

namespace App\PaymentMethods;

use App\Models\Currency;
use App\Events\OrderPaymentConfirmed;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Stripe\PaymentIntent;

class Stripe extends PaymentMethod
{
    function process(\App\PaymentMethod $dbMethod, Payment $payment, User $user)
    {
        $currency = Currency::getDefaultCurrency();
        $p = [
            'payment_method_types' => ['card'],
            'line_items' => [[
                'name' => 'Order ' . optional($payment->order)->track,
                'quantity' => 1,
                'amount' => $payment->gross_amount * 100,
                'currency' => $currency->code
            ]],
            'mode' => 'payment',
            'success_url' => route('payment.ipn', ['type' => 'order', 'ref' => $payment->track, 'session_id' => '{CHECKOUT_SESSION_ID}']),
            'cancel_url' => route('payment.failed', ['type' => 'order', 'ref' => $payment->track]),
        ];
        \Stripe\Stripe::setApiKey($dbMethod->cred2);
        $session = Session::create($p);
        $session_id = $session->id;
        $payment->metas()->updateOrCreate(['name' => 'stripe_session'], ['content' => $session_id]);
        return view('payment.stripe', compact('dbMethod', 'payment', 'user', 'currency', 'session_id'));
    }

    public function ipn(Request $request, \App\PaymentMethod $dbMethod, Payment $payment)
    {
        $stripe_session_ = $payment->metas()->where('name', 'stripe_session')->first();
        if (!$stripe_session_) return redirect()->route('payment.failed', ['type' => 'order', 'ref' => $payment->track, 'token' => request()->get('token')]);
        \Stripe\Stripe::setApiKey($dbMethod->cred2);
        try {
            $checkout_session = Session::retrieve($stripe_session_->content);
        } catch (\Exception $exception) {
            return redirect()->route('payment.failed', ['type' => 'order', 'ref' => $payment->track, 'token' => request()->get('token')])->withErrors($exception->getMessage());
        }
        if (!$checkout_session) redirect()->route('payment.failed', ['type' => 'order', 'ref' => $payment->track, 'token' => request()->get('token')])->withErrors('Transaction has been failed!');
        try {
            $intent = PaymentIntent::retrieve($checkout_session->payment_intent);
        } catch (ApiErrorException $e) {
            return redirect()->route('payment.failed', ['type' => 'order', 'ref' => $payment->track, 'token' => request()->get('token')])->withErrors($e->getMessage());
        }
        if (!$intent) return redirect()->route('payment.failed', ['type' => 'order', 'ref' => $payment->track, 'token' => request()->get('token')])->withErrors('Unable to fetch the transaction details!');
        if ($intent->status != 'succeeded') redirect()->route('payment.failed', ['type' => 'order', 'ref' => $payment->track, 'token' => request()->get('token')])->withErrors('Transaction has been failed!');
        DB::beginTransaction();
        try {
            $payment->update([
                'status' => 1,
                'transaction_id' => $intent->id
            ]);
            $payment->order->update([
                'status' => 0
            ]);
            $payment->order->addNote('Payment received via Stripe. TXN ID ' . $intent->id);
            event(new OrderPaymentConfirmed($payment));
        } catch (\Exception $exception) {
            DB::rollBack();
            return redirect()->route('payment.failed', ['type' => 'order', 'ref' => $payment->track])->withErrors($exception->getMessage());
        }
        DB::commit();
        return redirect()->route('payment.success', ['type' => 'order', 'ref' => $payment->track])->withSuccess('Payment successfully completed');
    }
}
