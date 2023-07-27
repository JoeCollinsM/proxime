<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuotationItems extends Model
{
    protected $fillable = [
        'quotation_id',
        'product_id',
        'price',
        'quantity',
        'tax_id',
        'tax',
    ];
}
