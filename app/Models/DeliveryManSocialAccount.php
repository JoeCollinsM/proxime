<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryManSocialAccount extends Model
{
    protected $guarded = ['id'];

    function user()
    {
        return $this->belongsTo(DeliveryMan::class, 'user_id', 'id');
    }
}
