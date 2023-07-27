<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
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

    static function getDefaultCurrency()
    {
        return self::query()->where('is_default', 1)->where('status', 1)->first();
    }
}
