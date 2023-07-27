<?php

namespace App\Models;

use App\Casts\CurrencyCast;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ShippingMethod extends Model
{
    protected $guarded = ['id'];
    protected $casts = [
        'charge' => CurrencyCast::class
    ];

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
}
