<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $guarded = ['id'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'caps' => 'array',
    ];

    function staffs()

    {

        return $this->hasMany(Shop::class, 'role_id', 'id');

    }
}
