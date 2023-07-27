<?php

namespace App\Models;

use App\Casts\CurrencyCast;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $guarded = ['id'];
    protected $casts = [
        'amount' => CurrencyCast::class
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

    function user()
    {
        return $this->morphTo();
    }

    function ref()
    {
        return $this->morphTo();
    }
}
