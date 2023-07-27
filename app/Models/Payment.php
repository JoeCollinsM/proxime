<?php

namespace App\Models;

use App\Casts\CurrencyCast;
use App\Helpers\MetaHolder;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use MetaHolder;

    protected $guarded = ['id'];
    protected $casts = [
        'net_amount' => CurrencyCast::class,
        'charge' => CurrencyCast::class,
        'gross_amount' => CurrencyCast::class,
    ];

    static function generateTrack()
    {
        $t = rand(100000, 999999);
        $exists = self::query()->where('track', $t)->count();
        while ($exists) {
            $t = rand(100000, 999999);
            $exists = self::query()->where('track', $t)->count();
        }
        return $t;
    }

    function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    function payment_method()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id', 'id');
    }
}
