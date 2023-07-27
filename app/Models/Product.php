<?php

namespace App\Models;

use App\Casts\CurrencyCast;
use App\Helpers\MetaHolder;
use App\Helpers\Reviewable;
use App\Helpers\Translatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use MetaHolder, Reviewable, Translatable;

    protected $translatable_columns = ['title', 'excerpt', 'content'];
    protected $guarded = ['id'];
    protected $appends = ['price_off', 'type', 'star'];
    protected $casts = [
        'sale_price' => CurrencyCast::class,
        'general_price' => CurrencyCast::class,
        'tax' => CurrencyCast::class
    ];

    protected static function boot()
    {
        parent::boot();

        $prefix = trim(request()->route()->getPrefix(), '/');
        $seg = explode('/', $prefix);
        $prefix = $seg[0];

        if (!in_array($prefix, ['staff', 'shop'])) {
            static::addGlobalScope('security', function (Builder $builder) {
                $builder->where('status', 1);
            });
            static::addGlobalScope('not_a_variant', function (Builder $builder) {
                $builder->whereNull('parent_id');
            });
            static::addGlobalScope('dependency', function (Builder $builder) {
                $builder->whereHas('shop')->whereHas('category');
            });
        }
    }

    function shop()
    {
        return $this->belongsTo(Shop::class, 'shop_id', 'id');
    }

    function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    function parent()
    {
        return $this->belongsTo(self::class, 'parent_id', 'id');
    }

    function variations()
    {
        return $this->hasMany(self::class, 'parent_id', 'id')->withoutGlobalScope('security')->withoutGlobalScope('not_a_variant')->withoutGlobalScope('dependency');
    }

    function attrs()
    {
        return $this->hasMany(ProductAttribute::class, 'product_id', 'id');
    }

    function tags()
    {
        return $this->belongsToMany(Tag::class, 'tag_product');
    }

    function isAvailable($quantity = 1)
    {
        if ($this->stock == -1) return true;
        return ($this->per*$quantity) <= $this->stock;
    }

    function getTypeAttribute($v)
    {
        return $this->variations()->count() ? 'variable' : 'simple';
    }

    function getDeliveryTimeAttribute($v)
    {
        if (!request()->is('api/*')) return $v;
        $r = $v . ' ';
        if ($this->delivery_time_type == 1) {
            $r .= __('hour(s)');
        } elseif ($this->delivery_time_type == 2) {
            $r .= __('day(s)');
        } elseif ($this->delivery_time_type == 3) {
            $r .= __('week(s)');
        } elseif ($this->delivery_time_type == 4) {
            $r .= __('month(s)');
        }
        return $r;
    }

    function getPriceOffAttribute($v)
    {
        if ($this->general_price == 0) return 0;
        return round(100 * (($this->general_price - $this->sale_price) / $this->general_price));
    }

    function sales()
    {
        // Actually Line Items
        return $this->hasMany(LineItem::class, 'product_id', 'id');
    }

    function variantSales()
    {
        // Actually Line Items
        return $this->hasMany(LineItem::class, 'variation_id', 'id');
    }
}
