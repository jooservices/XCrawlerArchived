<?php

namespace App\Models\Traits;

use App\Observers\JavThridPartyObserver;

trait HasMovieObserver
{
    protected static function bootHasMovieObserver()
    {
        static::observe(JavThridPartyObserver::class);
    }

    public static function findByDvdId(string $dvdId)
    {
        return self::where(['dvd_id' => $dvdId])->first();
    }
}
