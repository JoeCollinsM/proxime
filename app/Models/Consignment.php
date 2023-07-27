<?php

namespace App\Models;

use App\Casts\CurrencyCast;
use App\Helpers\Reviewable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Consignment extends Model
{
    use Reviewable;

    protected $guarded = ['id'];
    protected $dates = ['start_on', 'resolved_on'];
    protected $casts = [
        'images' => 'array',
        'commission' => CurrencyCast::class
    ];

    static function generateTrack()
    {
        $t = rand(100000, 999999);
        $exists = self::query()->where('track', $t)->count();
        while ($exists) {
            $t = rand(100000, 999999);
            $exists = self::query()->where('track', $t)->count();
        }
        return $t;
    }

    function commissionExist()
    {
        if (!$this->delivery_man) return true;
        /* @var DeliveryMan $man */
        $man = $this->delivery_man;
        return $man->transactions()->where('ref_type', get_class($this))->where('ref_id', $this->id)->where('matter', 'commission')->exists();
    }

    public function setStartOnAttribute($date)

    {

        $this->attributes['start_on'] = Carbon::createFromFormat('d-m-Y', $date);

    }

    function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    function orders()
    {
        return $this->hasMany(Order::class, 'id', 'order_id');
    }

    function address()
    {
        return $this->hasOneThrough(OrderAddress::class, Order::class, 'id', 'order_id', 'order_id', 'id');
    }

    function items()
    {
        return $this->hasManyThrough(LineItem::class, Order::class, 'id', 'order_id', 'order_id', 'id');
    }

    function delivery_man()
    {
        return $this->belongsTo(DeliveryMan::class, 'delivery_man_id', 'id');
    }
}
