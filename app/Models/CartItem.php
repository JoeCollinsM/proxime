<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $guarded = ['id'];
    protected $appends = ['gross_total', 'net_total', 'tax_total'];
    protected $casts = [
        'attrs' => 'array'
    ];

    function cart()
    {
        return $this->belongsTo(Cart::class, 'cart_id', 'id');
    }

    function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id')->withoutGlobalScope('not_a_variant');
    }

    function variant()
    {
        return $this->belongsTo(Product::class, 'variation_id', 'id')->withoutGlobalScope('not_a_variant');
    }

    function getGrossTotalAttribute()
    {
        return $this->net_total + $this->tax_total;
    }

    function getNetTotalAttribute()
    {
        return $this->price * $this->quantity;
    }

    function getTaxTotalAttribute()
    {
        return $this->tax * $this->quantity;
    }
}
