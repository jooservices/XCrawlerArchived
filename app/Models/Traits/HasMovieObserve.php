<?php

namespace App\Models\Traits;

use App\Observers\JavThirdPartyObserver;

trait HasMovieObserve
{
    protected static function bootHasMovieObserve()
    {
        static::observe(JavThirdPartyObserver::class);
    }

    public static function findByDvdId(string $dvdId)
    {
        return self::where(['dvd_id' => $dvdId])->first();
    }
}
