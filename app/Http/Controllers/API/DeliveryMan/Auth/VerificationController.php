<?php

namespace App\Http\Controllers\API\DeliveryMan\Auth;

use App\Models\DeliveryMan;
use App\Helpers\API\Formatter;
use App\Notifications\SendEmailAndSmsVerificationOtp;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\Request;

class VerificationController
{
    use Formatter;

    function emailResend(Request $request)
    {
        /* @var DeliveryMan $user */
        $user = $request->user('delivery_man');
        if ($user->hasVerifiedEmail()) return abort(404);
        $otp = rand(100000, 999999);
        $user->update([
            'email_otp' => $otp
        ]);
        $user->notify(new SendEmailAndSmsVerificationOtp($otp));
        return $this->response('OTP Send Successfully');
    }

    function emailVerify(Request $request)
    {
        /* @var DeliveryMan $user */
        $user = $request->user('delivery_man');
        if ($user->hasVerifiedEmail()) return abort(404);
        $request->validate([
            'otp' => 'required|numeric'
        ]);
        if ($request->otp != $user->email_otp) return $this->withErrors('OTP not matched');
        $user->update([
            'email_verified_at' => now()
        ]);
        return $this->response('Email address verified successfully');
    }

    function smsResend(Request $request)
    {
        /* @var DeliveryMan $user */
        $user = $request->user('delivery_man');
        if ($user->phone_verified_at != null) return abort(404);
        $otp = rand(100000, 999999);
        $user->update([
            'sms_otp' => $otp
        ]);
        $signature = $request->app_signature;
        $user->notify(new SendEmailAndSmsVerificationOtp($otp, 'sms', $signature));
        return $this->response('OTP Send Successfully');
    }

    function smsVerify(Request $request)
    {
        /* @var DeliveryMan $user */
        $user = $request->user('delivery_man');
        if ($user->phone_verified_at != null) return abort(404);
        $request->validate([
            'otp' => 'required|numeric'
        ]);
        if ($request->otp != $user->sms_otp) return $this->withErrors('OTP not matched');
        $user->update([
            'phone_verified_at' => now()
        ]);
        return $this->response('Phone number verified successfully');
    }
}
