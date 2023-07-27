<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $guarded = ['id'];

    function reviewable()
    {
        return $this->morphTo();
    }

    function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    function getRatingHtmlAttribute()
    {
        $yellow = $this->rating;
        $blank = 5-$this->rating;
        $markup = '';
        for ($i = 0;$i < $yellow;$i++) {
            $markup .= '<i class="fa fa-star text-warning"></i>';
        }
        for ($i = 0;$i < $blank;$i++) {
            $markup .= '<i class="fa fa-star-o"></i>';
        }
        return '<span id="rating-' . $this->id . '">' . $markup . '</span>';
    }
}
