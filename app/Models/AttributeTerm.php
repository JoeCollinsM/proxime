<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttributeTerm extends Model
{
    protected $guarded = ['id'];

    function attribute()
    {
        return $this->belongsTo(Attribute::class, 'attribute_id', 'id');
    }

    function product_count()
    {
        return ProductAttribute::query()->where('name', $this->attribute->slug)->where('content', $this->slug)->groupBy('product_id')->count();
    }
}
