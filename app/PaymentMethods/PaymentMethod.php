<?php
namespace App\PaymentMethods;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;

abstract class PaymentMethod
{
    function process(\App\PaymentMethod $dbMethod, Payment $payment, User $user) {

    }

    function ipn(Request $request, \App\PaymentMethod $dbMethod, Payment $payment)
    {

    }
}
