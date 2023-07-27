<?php

namespace App\Models;

use App\Helpers\MetaHolder;
use App\Helpers\Translatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use MetaHolder, Translatable;

    protected $translatable_columns = [];
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

    function parent()
    {
        return $this->belongsTo(self::class, 'parent_id', 'id');
    }

    function children()
    {
        return $this->hasMany(self::class, 'parent_id', 'id');
    }

    function products()
    {
        return $this->hasMany(Product::class, 'category_id', 'id');
    }

    function tags()
    {
        return $this->belongsToMany(Tag::class, 'tag_category');
    }

    function sales()
    {
        return $this->hasManyThrough(LineItem::class, Product::class, 'category_id', 'product_id', 'id', 'id');
    }
}
