<?php

namespace App\Models;

use App\Casts\CurrencyCast;
use Illuminate\Database\Eloquent\Model;

class Withdraw extends Model
{
    protected $guarded = ['id'];
    protected $casts = [
        'fields' => 'array',
        'amount' => CurrencyCast::class,
        'charge' => CurrencyCast::class
    ];

    public function user()
    {
        return $this->morphTo();
    }

    public function method()
    {
        return $this->belongsTo(WithdrawMethod::class, 'withdraw_method_id', 'id');
    }
}
