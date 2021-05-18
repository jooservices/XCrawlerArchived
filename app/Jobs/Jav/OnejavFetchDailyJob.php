<?php

namespace App\Jobs\Jav;

use App\Models\Onejav;
use App\Services\Crawler\OnejavCrawler;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\RateLimitedMiddleware\RateLimited;

/**
 * This job will be dispatched every day at 12:00,
 * so we won't need unique job check.
 * @package App\Jobs
 */
class OnejavFetchDailyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
                ->allow(2) // Allow 2 jobs
                ->everySecond() // In second
                ->releaseAfterMinutes(30); // Release back to pool after 60 minutes

            return [$rateLimitedMiddleware];
        }

        return [];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $crawler = app(OnejavCrawler::class);
        $items = $crawler->daily();
        $items->each(function ($item) {
            Onejav::firstOrCreate(['url' => $item->get('url')], $item->toArray());
        });
    }
}
