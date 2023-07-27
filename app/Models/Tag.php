<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $guarded = ['id'];

    function categories()
    {
        return $this->belongsToMany(Category::class, 'tag_category');
    }

    function products()
    {
        return $this->belongsToMany(Product::class, 'tag_product');
    }
}
