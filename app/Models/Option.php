<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    protected $guarded = ['id'];
    private static $autoload;

    public static function getOption($name, $default = null)

    {

        if (!self::$autoload) {
            self::$autoload = self::pluck('content', 'name')->toArray();
        }

        if (!isset(self::$autoload[$name])) return $default;

        return self::$autoload[$name];

    }

    public static function updateOption($name, $value)

    {

        return (bool) self::updateOrCreate(['name' => $name], ['content' => $value]);

    }
}
