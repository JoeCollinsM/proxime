<?php
namespace App\Helpers;

use App\Models\Review;

trait Reviewable
{
    function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    function getStarAttribute()

    {
        if ($this->reviews()->count() == 0) return 0;
        return round(($this->reviews()->sum('rating')/$this->reviews()->count()));
    }

    function getRatingHtmlAttribute()
    {
        $yellow = $this->star;
        $blank = 5-$this->star;
        $markup = '';
        for ($i = 0;$i < $yellow;$i++) {
            $markup .= '<i class="fa fa-star text-warning"></i>';
        }
        for ($i = 0;$i < $blank;$i++) {
            $markup .= '<i class="fa fa-star-o"></i>';
        }
        return '<span id="rating-shop-' . $this->id . '">' . $markup . '</span>';
    }
}
