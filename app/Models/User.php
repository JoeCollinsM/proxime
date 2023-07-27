<?php

namespace App\Models;

use App\Casts\CurrencyCast;
use App\Helpers\HasTransaction;
use App\Helpers\JWT;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements MustVerifyEmail, JWTSubject
{
    use Notifiable, JWT, HasTransaction;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'username', 'avatar', 'email_verified_at', 'phone_verified_at', 'push_notification', 'email_otp', 'sms_otp', 'device_token', 'status', 'email', 'phone', 'balance', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'balance' => CurrencyCast::class
    ];

    function carts()
    {
        return $this->hasMany(Cart::class, 'user_id', 'id');
    }

    function addresses()
    {
        return $this->hasMany(Address::class, 'user_id', 'id');
    }

    function socialAccounts()
    {
        return $this->hasMany(SocialAccount::class, 'user_id', 'id');
    }

    function followings()
    {
        return $this->belongsToMany(Shop::class, 'followings');
    }

    function orders()
    {
        return $this->hasMany(Order::class, 'user_id', 'id');
    }

    function reviews()
    {
        return $this->hasMany(Review::class, 'user_id', 'id');
    }

    function payments()
    {
        return $this->hasManyThrough(Payment::class, Order::class, 'user_id', 'order_id', 'id', 'id');
    }

    public function routeNotificationForFcm()
    {
        return $this->device_token;
    }

    public function routeNotificationForTwilio()
    {
        return $this->phone;
    }
}
