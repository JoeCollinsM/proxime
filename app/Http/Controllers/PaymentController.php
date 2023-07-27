<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController
{
    function detectPayment(Request $request, $type, $ref)
    {
        if (!in_array($type, ['order'])) return abort(404);
        if ($type == 'order') {
            /* @var Payment|null $payment */
            $payment = Payment::query()->where('track', $ref)->firstOrFail();
            if ($payment->status != 0 || now()->subHours(2)->gt($payment->created_at)) return abort(404);
            /* @var User $user */
            $user = $payment->order->user;
            if (!$user instanceof User) return abort(404);
            $payment_method = $payment->payment_method;
            if (!$payment_method instanceof PaymentMethod) return abort(404);
            $processor_class = $payment_method->class_name;
            $processor = new $processor_class;
            return $processor->process($payment_method, $payment, $user);
        }
        return abort(404);
    }

    function ipnReceiver(Request $request, $type, $ref)
    {
        if (!in_array($type, ['order'])) return abort(404);
        if ($type == 'order') {
            /* @var Payment|null $payment */
            $payment = Payment::query()->where('track', $ref)->firstOrFail();
            if ($payment->status != 0 || now()->subHours(2)->gt($payment->created_at)) return abort(404);
            $payment_method = $payment->payment_method;
            if (!$payment_method instanceof PaymentMethod) return abort(404);
            $processor_class = $payment_method->class_name;
            $processor = new $processor_class;
            return $processor->ipn($request, $payment_method, $payment);
        }
        return abort(404);
    }

    function successPayment($type, $ref)
    {
        if (!in_array($type, ['order'])) return abort(404);
        /* @var Payment|null $payment */
        $payment = Payment::query()->where('track', $ref)->firstOrFail();
        if ($type == 'order') {
            $order = $payment->order;
            return view('payment.success', compact('type', 'ref', 'payment', 'order'));
        }
        return view('payment.success', compact('type', 'ref', 'payment'));
    }

    function failedPayment($type, $ref)
    {
        if (!in_array($type, ['order'])) return abort(404);
        /* @var Payment|null $payment */
        $payment = Payment::query()->where('track', $ref)->firstOrFail();
        if ($type == 'order') {
            $order = $payment->order;
            return view('payment.failed', compact('type', 'ref', 'payment', 'order'));
        }
        return view('payment.failed', compact('type', 'ref', 'payment'));
    }
}
