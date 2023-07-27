<?php

namespace App\Http\Controllers\API\Auth;

use App\Helpers\API\Formatter;
use App\Notifications\SendPasswordResetOtp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ForgotPasswordController
{
    use Formatter;

    function sendPasswordResetOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);
        /* @var User $user */
        $user = User::query()->where('email', $request->email)->where('status', 1)->first();
        if (!$user) {
            throw ValidationException::withMessages(['email' => 'No user found for this email']);
        }
        $otp = rand(100000, 999999);
        $forgot_password_via = config('proxime.forgot_password_via', 'email');
        $user->update([
            $forgot_password_via . '_otp' => $otp
        ]);
        $user->notify(new SendPasswordResetOtp($otp, $forgot_password_via));
        return $this->withSuccess('Password reset OTP send successfully');
    }

    function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|numeric',
            'password' => 'required|min:6|confirmed'
        ]);
        /* @var User $user */
        $user = User::query()->where('email', $request->email)->firstOrFail();
        $forgot_password_via = config('proxime.forgot_password_via', 'email');
        $k = $forgot_password_via . '_otp';
        if ($user->{$k} != $request->otp) return $this->withErrors('OTP not matched');

        $user->update([
            'password' => Hash::make($request->password),
            $k => null
        ]);
        return $this->withSuccess('Password reset successfully');
    }
}
