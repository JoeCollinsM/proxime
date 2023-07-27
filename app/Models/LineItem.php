<?php

namespace App\Models;

use App\Casts\CurrencyCast;
use Illuminate\Database\Eloquent\Model;

class LineItem extends Model
{
    protected $guarded = ['id'];
    protected $casts = [
        'attrs' => 'array',
        'price' => CurrencyCast::class,
        'tax' => CurrencyCast::class
    ];

    function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id')->withoutGlobalScope('not_a_variant');
    }

    function variant()
    {
        return $this->belongsTo(Product::class, 'variation_id', 'id')->withoutGlobalScope('not_a_variant');
    }
}
