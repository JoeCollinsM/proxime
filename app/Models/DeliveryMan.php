<?php

namespace App\Models;


use App\Casts\CurrencyCast;
use App\Helpers\HasTransaction;
use App\Helpers\JWT;
use App\Helpers\Reviewable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class DeliveryMan extends Authenticatable implements MustVerifyEmail, JWTSubject
{
    use Notifiable, JWT, Reviewable, HasTransaction;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'username', 'avatar', 'email_verified_at', 'phone_verified_at', 'email_otp', 'sms_otp', 'device_token', 'status', 'email', 'phone', 'balance', 'password',
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

    function consignments()
    {
        return $this->hasMany(Consignment::class, 'delivery_man_id', 'id');
    }

    function withdraws()
    {
        return $this->morphMany(Withdraw::class, 'user');
    }

    public function routeNotificationForFcm()
    {
        return $this->device_token;
    }

    public function routeNotificationForTwilio()
    {
        return $this->phone;
    }

    function socialAccounts()
    {
        return $this->hasMany(DeliveryManSocialAccount::class, 'user_id', 'id');
    }
}
