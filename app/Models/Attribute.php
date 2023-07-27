<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    protected $guarded = ['id'];

    function terms()
    {
        return $this->hasMany(AttributeTerm::class, 'attribute_id', 'id');
    }
}
