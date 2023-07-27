<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductAttribute extends Model
{
    protected $guarded = ['id'];

    function attribute()
    {
        return $this->belongsTo(Attribute::class, 'attribute_id', 'id');
    }

    function setContentAttribute($content)
    {
        if (is_array($content)) {
            $this->attributes['content'] = implode('|', $content);
        } else {
            $this->attributes['content'] = $content;
        }
    }

    function getContentAttribute($content)
    {
        if (is_numeric(strpos($content, '|'))) {
            return explode('|', $content);
        }
        return $content;
    }
}
