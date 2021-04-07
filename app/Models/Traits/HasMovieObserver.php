<?php

namespace App\Models\Traits;

use App\Observers\JavMovieObserver;

trait HasMovieObserver
{
    protected static function bootHasMovieObserver()
    {
        static::observe(JavMovieObserver::class);
    }
}
