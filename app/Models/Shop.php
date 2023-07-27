<?php

namespace App\Models;

use App\Casts\CurrencyCast;
use App\Helpers\HasTransaction;
use App\Helpers\MetaHolder;
use App\Helpers\Reviewable;
use App\Notifications\ShopResetPassword;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Shop extends Authenticatable implements MustVerifyEmail
{
    use Notifiable, HasTransaction, MetaHolder, Reviewable;

    protected $guarded = ['id'];
    protected $dates = ['opening_at', 'closing_at'];
    protected $casts = [
        'balance' => CurrencyCast::class
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected static function boot()
    {
        parent::boot();
        $prefix = trim(request()->route()->getPrefix(), '/');
        $seg = explode('/', $prefix);
        $prefix = $seg[0];

        if (!in_array($prefix, ['staff'])) {
            static::addGlobalScope('security', function (Builder $builder) {
                $builder->where('status', 1);
            });
        }

    }

    function products()

    {
        return $this->hasMany(Product::class, 'shop_id', 'id');
    }

    function withdraws()
    {
        return $this->morphMany(Withdraw::class, 'user');
    }

    function order_items()
    {
        return $this->hasManyThrough(LineItem::class, Product::class);
    }

    function category()

    {
        return $this->belongsTo(ShopCategory::class, 'shop_category_id', 'id')->withoutGlobalScope('security');
    }

    function followers()
    {
        return $this->belongsToMany(User::class, 'followings');
    }

    function setOpeningAtAttribute($v)
    {
        $this->attributes['opening_at'] = Carbon::createFromFormat("h:i a", $v);
    }

    function setClosingAtAttribute($v)
    {
        $this->attributes['closing_at'] = Carbon::createFromFormat("h:i a", $v);
    }

    function getOpeningStatusAttribute()

    {
        $t1 = Carbon::today()->setTimeFrom($this->opening_at);
        $t2 = Carbon::today()->setTimeFrom($this->closing_at);
        $now = now();
        return $t1->lte($now) && $t2->gte($now);

    }

    function getIsFollowingAttribute()
    {
        /* @var User $user */
        $user = request()->user('api');
        return $this->followers()->where('users.id', $user->id)->exists();
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ShopResetPassword($token));
    }
}
