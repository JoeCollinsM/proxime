<?php

namespace App\Models;

use App\PaymentMethods\CashOnDelivery;
use App\PaymentMethods\PayPal;
use App\PaymentMethods\PerfectMoney;
use App\PaymentMethods\RazorPay;
use App\PaymentMethods\Skrill;
use App\PaymentMethods\Stripe;
use App\PaymentMethods\MPesa;
use App\PaymentMethods\Wallet;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $guarded = ['id'];

    protected static function boot()
    {
        parent::boot();
        $prefix = trim(request()->route()->getPrefix(), '/');
        $seg = explode('/', $prefix);
        $prefix = $seg[0];

        if (!in_array($prefix, ['staff'])) {
            static::addGlobalScope('security', function (Builder $builder) {
                $builder->where('status', '!=', 0);
            });
        }

    }

    public static function generateMethods()
    {
        $methods = [
            [
                'name' => 'Paypal',
                'description' => 'pay with paypal',
                'min' => -1,
                'max' => -1,
                'cred1' => 'yhossainshanto@gmail.com',
                'cred2' => NULL,
                'percent_charge' => 1,
                'fixed_charge' => 1,
                'status' => 1,
                'class_name' => PayPal::class
            ],
            [
                'name' => 'PerfectMoney',
                'description' => 'pay by PerfectMoney',
                'min' => -1,
                'max' => -1,
                'cred1' => 'G079qn4Q7XATZBqyoCkBteGRg',
                'cred2' => 'U5376900',
                'percent_charge' => 1,
                'fixed_charge' => 1,
                'status' => 1,
                'class_name' => PerfectMoney::class
            ],
            [
                'name' => 'Stripe',
                'description' => 'Pay by card',
                'min' => -1,
                'max' => -1,
                'cred1' => 'pk_test_51H7CQICR36Tusvyuhj6WRYfLg6QlLZ8CM5mgRcI285vuo5EIw9FvmlsSIcRpcxOdocFBpPneDuJdGSvgHqREvrSR00Pnm6ekPw',
                'cred2' => 'sk_test_51H7CQICR36Tusvyuk0WqHebWIEW7B1TZeTqg1aOFnuh0BSkG1KA2AsUVDJNyzXsWg3j8FffXP0iD2tFN1dCwChSO0017QCajdW',
                'percent_charge' => 1,
                'fixed_charge' => 1,
                'status' => 1,
                'class_name' => Stripe::class
            ],
            [
                'name' => 'Skrill',
                'description' => 'Pay using skrill',
                'min' => -1,
                'max' => -1,
                'cred1' => 'merchant@skrill.com',
                'cred2' => 'ItechSoftSolution',
                'percent_charge' => 1,
                'fixed_charge' => 1,
                'status' => 1,
                'class_name' => Skrill::class
            ],
            [
                'name' => 'Cash On Delivery',
                'description' => 'Pay using COD',
                'min' => -1,
                'max' => -1,
                'cred1' => null,
                'cred2' => null,
                'percent_charge' => 1,
                'fixed_charge' => 1,
                'status' => 1,
                'class_name' => CashOnDelivery::class
            ],
            [
                'name' => 'Razorpay',
                'description' => 'Pay using Razorpay',
                'min' => -1,
                'max' => -1,
                'cred1' => 'rzp_test_9TQ3frkFhOObc2',
                'cred2' => 'QVayjR3vKGxMo1M5vtfIVV0R',
                'percent_charge' => 1,
                'fixed_charge' => 1,
                'status' => 1,
                'class_name' => RazorPay::class
            ],
            [
                'name' => 'Mpesa',
                'description' => 'Pay using Mpesa',
                'min' => -1,
                'max' => -1,
                'cred1' => null,
                'cred2' => null,
                'percent_charge' => 1,
                'fixed_charge' => 0,
                'status' => 1,
                'class_name' => MPesa::class
            ],
            [
                'name' => 'Wallet',
                'description' => 'Pay using Wallet',
                'min' => -1,
                'max' => -1,
                'cred1' => null,
                'cred2' => null,
                'percent_charge' => 0,
                'fixed_charge' => 0,
                'status' => 1,
                'class_name' => Wallet::class
            ]
        ];
        foreach ($methods as $method) {
            self::create($method);
        }
    }
}
