<?php

namespace App\Http\Controllers\Shop\Auth;


use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\Currency;
use App\Models\Shop;
use App\Models\ShopCategory;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
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
        $this->middleware('guest:shop');
    }

    public function showRegistrationForm()
    {
        $categories = ShopCategory::query()->where('status', 1)->get();
        $currency = Currency::getDefaultCurrency();
        return view('shop.auth.register', compact('categories', 'currency'));
    }

    /**
     * Handle a registration request for the application.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse|Response
     */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        $shop = $this->create($request->all());

        // if ($user->status == 1) {
        //     $token = $this->guard()->login($user);
        //     return $this->withSuccess([
        //         'token' => $token,
        //         'user' => $user
        //     ]);
        // }
        return redirect()->route('shop.login')->withSuccess('Shop created successfully');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $rules = [
            'shop_category_id' => 'required|exists:shop_categories,id',
            'name' => 'required|string|max:255',
            'slug' => 'required|slug|max:255|unique:shops,slug',
            // 'logo' => 'required|max:255',
            // 'cover' => 'required|max:255',
            'address' => 'required|max:255',
            'latitude' => 'required|numeric|max:255',
            'longitude' => 'required|numeric|max:255',
            'opening_at' => 'required|max:255|date_format:"h:i a"',
            'closing_at' => 'required|max:255|date_format:"h:i a"',
            'details' => 'nullable',
            'vendor_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:shops,email',
            'phone' => 'nullable|numeric|unique:shops,phone',
            'password' => 'required|min:8|confirmed',
            'minimum_order' => 'required|numeric',
        ];
        return Validator::make($data, $rules);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param array $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        $data['sms_verification'] = config('proxime.sms_verification')?null:now();
        $data['email_verification'] = config('proxime.email_verification')?null:now();
        $data['status'] = config('proxime.default_vendor_status', 1);
        $data['password'] = Hash::make($data['password']);

        if ($data['minimum_order'] < 0) {
            $data['minimum_order'] = -1;
        }
        DB::beginTransaction();
        try {
            $shop = Shop::create($data);
            if (!$shop) throw new \Exception('Unable to create shop');
            // if (is_array($request->meta)) {
            //     $metas = [];
            //     foreach ($request->meta as $name => $content) {
            //         $metas[] = ['name' => $name, 'content' => $content];
            //     }
            //     $shop->metas()->createMany($metas);
            // }
        } catch (\Exception $exception) {
            DB::rollBack();
            return redirect()->back()->withErrors('Unable to create new shop');
        }
        DB::commit();
        
    }

    /**
     * Get the guard to be used during registration.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard('shop');
    }
}
