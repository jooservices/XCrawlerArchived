<?php

namespace App\Models\Traits;

trait HasName
{
    public static function findByName(string $name)
    {
        return self::where('name', $name)->first();
    }
}
