<?php

namespace App\Models;

use App\Helpers\MetaHolder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ShopCategory extends Model
{
    use MetaHolder;

    protected $guarded = ['id'];

    protected static function boot()
    {
        parent::boot();
        $prefix = trim(request()->route()->getPrefix(), '/');
        $seg = explode('/', $prefix);
        $prefix = $seg[0];

        if (!in_array($prefix, ['staff'])) {
            static::addGlobalScope('security', function (Builder $builder) {
                $builder->where('status', '!=', 0);
            });
        }

    }

    function shops()
    {
        return $this->hasMany(Shop::class, 'shop_category_id', 'id');
    }

    function parent()
    {
        return $this->belongsTo(self::class, 'parent_id', 'id');
    }

    function children()
    {
        return $this->hasMany(self::class, 'parent_id', 'id');
    }
}
