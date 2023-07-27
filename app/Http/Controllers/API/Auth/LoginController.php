<?php

namespace App\Http\Controllers\API\Auth;

use App\Helpers\API\Formatter;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;
use Tymon\JWTAuth\JWTAuth;

class LoginController extends Controller
{
    use Formatter;

    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest:api')->except('logout');
    }

    /**
     * Handle a login request to the application.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\Response|void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Handle a login request to the application.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\Response|void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function socialLogin(Request $request)
    {
        $request->validate([
            'provider' => 'required|in:facebook,google',
            'provider_user_id' => 'required',
            'name' => 'required|string|max:191',
            'email' => 'nullable|email|max:191',
        ]);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        try {
            $user = User::query()->whereHas('socialAccounts', function ($q) use ($request) {
                /* @var Builder $q */
                $q->where('provider', $request->provider)->where('provider_id', $request->provider_user_id);
            })->where('status', 1)->first();

            if (!$user) {
                $request->validate([
                    'name' => 'required|string|max:191',
                    'email' => 'nullable|email|max:191|unique:users,email'
                ]);
                $n = $request->name;
                $u = Str::slug($n);
                $e = $request->email ?? $u . '@' . $request->provider . '.com';
                $status = config('proxime.default_user_status', 1);
                /* @var User $user */
                $user = User::create([
                    'name' => $n,
                    'username' => $u,
                    'email' => $e,
                    'phone' => null,
                    'password' => Hash::make($request->provider_user_id),
                    'email_verified_at' => now(),
                    'phone_verified_at' => now(),
                    'status' => $status
                ]);
                $user->socialAccounts()->create([
                    'provider' => $request->provider,
                    'provider_id' => $request->provider_user_id,
                ]);
            }

            if ($user->status == 1) {
                $token = $this->guard()->login($user);
                $this->guard()->setToken($token);
                return $this->sendLoginResponse($request);
            }
        } catch (\Exception $exception) {
            // If the login attempt was unsuccessful we will increment the number of attempts
            // to login and redirect the user back to the login form. Of course, when this
            // user surpasses their maximum number of attempts they will get locked out.
            $this->incrementLoginAttempts($request);
            throw ValidationException::withMessages([
                'error' => $exception->getMessage(),
            ]);
        }
    }

    /**
     * Send the response after the user was authenticated.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|Response
     */
    protected function sendLoginResponse(Request $request)
    {
        $this->clearLoginAttempts($request);

        $token = (string)$this->guard()->getToken();
        $expiration = $this->guard()->getPayload()->get('exp');

        if ($request->device_token) {
            try {
                if (filter_var($request->username, FILTER_VALIDATE_EMAIL)) {
                    User::query()->where('email', $request->username)->where('status', 1)->update([
                        'device_token' => $request->device_token
                    ]);
                } else {
                    User::query()->where($this->username(), $request->username)->where('status', 1)->update([
                        'device_token' => $request->device_token
                    ]);
                }
            } catch (\Exception $exception) {

            }
        }

        return $this->withSuccess([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $expiration - time(),
            'email_verified_at' => $this->guard()->user()->email_verified_at,
            'phone_verified_at' => $this->guard()->user()->phone_verified_at,
        ]);
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        if (filter_var($request->username, FILTER_VALIDATE_EMAIL)) {
            return ['email' => $request->username, 'password' => $request->password, 'status' => 1];
        }
        return [$this->username() => $request->username, 'password' => $request->password, 'status' => 1];
    }

    /**
     * Attempt to log the user into the application.
     *
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
        $token = $this->guard()->attempt($this->credentials($request));
        if (!$token) {
            return false;
        }

        $this->guard()->setToken($token);

        return true;
    }

    public function username()
    {
        return 'username';
    }

    protected function guard()
    {
        return Auth::guard();
    }

    /**
     * Log the user out of the application.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();

        if ($response = $this->loggedOut($request)) {
            return $response;
        }

        return $this->withSuccess(['message' => __('Successfully logged out')]);
    }
}
