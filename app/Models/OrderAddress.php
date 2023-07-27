<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderAddress extends Model
{
    protected $guarded = ['id'];

    function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }
}
