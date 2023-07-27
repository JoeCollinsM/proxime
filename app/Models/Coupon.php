<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $guarded = ['id'];
    protected $dates = ['start_at', 'expire_at'];

    public function setStartAtAttribute($date)

    {

        $this->attributes['start_at'] = Carbon::createFromFormat('d-m-Y', $date);

    }

    public function setExpireAtAttribute($date)

    {

        $this->attributes['expire_at'] = Carbon::createFromFormat('d-m-Y', $date);

    }

    public function users()

    {

        return $this->belongsToMany(User::class, 'user_coupon');

    }

    public function products()

    {

        return $this->belongsToMany(Product::class, 'product_coupon');

    }

    public function orders()

    {

        return $this->hasMany(Order::class, 'coupon_id', 'id');

    }

    public function getUsedAttribute($val)
    {
        return $this->orders()->count();
    }

    public function getStatusAttribute($val)

    {

        if (Carbon::now()->between($this->start_at, $this->expire_at)) return 1;
        if (Carbon::now()->lt($this->start_at)) return 2;
        return 0;

    }
}
