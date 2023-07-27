<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class OrderNote extends Model
{
    protected $guarded = ['id'];

    protected static function boot()
    {
        parent::boot();
        $prefix = trim(request()->route()->getPrefix(), '/');
        $seg = explode('/', $prefix);
        $prefix = $seg[0];
        $context = [1];
        if (!in_array($prefix, ['staff'])) {
            // Outside of staff panel
            $context[] = 3;
        } else {
            // Inside of Staff panel
            $context[] = 2;
        }
        static::addGlobalScope('security', function (Builder $builder) use ($context) {
            $builder->whereIn('context', $context);
        });
    }

    function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }
}
