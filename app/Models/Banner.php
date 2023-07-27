<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $guarded = ['id'];

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'tag_banner');
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class, 'shop_id', 'id');
    }

    public function products()
    {
        $tag_ids = $this->tags()->pluck('tags.id')->toArray();
        return Product::query()->whereHas('tags', function ($q) use ($tag_ids) {
           /* @var Builder $q */
           $q->whereIn('tags.id', $tag_ids);
        });
    }
}
