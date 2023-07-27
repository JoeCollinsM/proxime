<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $guarded = ['id'];
    protected $appends = ['gross_total', 'net_total', 'tax_total'];

    function items()
    {
        return $this->hasMany(CartItem::class, 'cart_id', 'id');
    }

    function coupon()
    {
        return $this->belongsTo(Coupon::class, 'coupon_id', 'id');
    }

    /**
     * @param ShippingMethod|null $shippingMethod
     */
    function is_free_shipping($shippingMethod = null)
    {
        $is_free_shipping = true;
        $not_free_shipping_supported_in_cart = $this->items()->whereHas('product', function ($q) {
            /* @var Builder $q */
            $q->where('is_free_shipping', 0);
        })->count();
        if ($not_free_shipping_supported_in_cart) {
            // If found at least one product which is not supporting free shipping feature
            $is_free_shipping = false;
        }
        // TODO: upcoming, check agreement with shipping method
        return $is_free_shipping;
    }

    function getNetTotalAttribute()
    {
        return round($this->items->sum('net_total'), config('proxime.decimals'));
    }

    function getTaxTotalAttribute()
    {
        return round($this->items->sum('tax_total'), config('proxime.decimals'));
    }

    function getGrossTotalAttribute()
    {
        return round(($this->net_total+$this->tax_total-$this->coupon_discount), config('proxime.decimals'));
    }
}
