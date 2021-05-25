<?php

namespace App\Models\Traits;

use App\Observer\JavThirdPartyObserve;

trait HasMovieObserve
{
    protected static function bootHasMovieObserve()
    {
        static::observe(JavThirdPartyObserve::class);
    }

    public static function findByDvdId(string $dvdId)
    {
        return self::where(['dvd_id' => $dvdId])->first();
    }
}
