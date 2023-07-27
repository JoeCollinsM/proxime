<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnedItems extends Model
{
    protected $fillable = [
        'return_id',
        'product_id',
        'price',
        'quantity',
        'tax_id',
        'tax',
    ];
}
