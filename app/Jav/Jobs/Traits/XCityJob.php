<?php

namespace App\Jav\Jobs\Traits;

use Spatie\RateLimitedMiddleware\RateLimited;

trait XCityJob
{
    /**
     * Determine the time at which the job should timeout.
     *
     * @return \DateTime
     */
    public function retryUntil()
    {
        return now()->addHours(6);
    }

    /**
     * Attempt 1: Release after 60 seconds
     * Attempt 2: Release after 180 seconds
     * Attempt 3: Release after 420 seconds
     * Attempt 4: Release after 900 seconds
     */
    public function middleware()
    {
        if (config('app.env') !== 'testing') {
            $rateLimitedMiddleware = (new RateLimited())
                ->allow(3) // Allow 3 jobs
                ->everySecond()
                ->releaseAfterMinutes(10); // Release back to pool after 60 seconds

            return [$rateLimitedMiddleware];
        }

        return [];
    }
}
