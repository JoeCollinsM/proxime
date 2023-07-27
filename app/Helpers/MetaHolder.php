<?php
namespace App\Helpers;


use App\Models\Meta;
use Illuminate\Database\Eloquent\Model;

trait MetaHolder
{
    function metas()
    {
        /* @var Model $this */
        return $this->morphMany(Meta::class, 'holder');
    }
}
