<?php

namespace App\Http\Middleware;

use App\Helpers\API\Formatter;
use Closure;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Redirect;

class EnsureEmailAndPhoneAreVerified
{
    use Formatter;

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next, ...$guards)
    {
        $g = is_array($guards) ? $guards[0] : $guards;
        $p = $g != 'delivery_man' ? 'customer' : 'delivery-man';
        if (!$request->user($g) ||
            ($request->user($g) instanceof MustVerifyEmail &&
                !$request->user($g)->hasVerifiedEmail())) {
            return $request->expectsJson()
                ? $this->response('Your email address is not verified.', 'email_unverified')
                : Redirect::route($p . 'verification.notice');
        }

        if (!$request->user($g) || ($request->user($g) && $request->user($g)->phone_verified_at == null)) {
            return $request->expectsJson()
                ? $this->response('Your phone number is not verified.', 'sms_unverified')
                : Redirect::route($p . 'verification.notice');
        }
        return $next($request);
    }
}
