<?php

namespace App\Jobs;

use App\Models\Onejav;
use App\Models\XCrawlerLog;
use App\Services\Crawler\OnejavCrawler;
use App\Services\Jav\OnejavService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\RateLimitedMiddleware\RateLimited;
use Throwable;

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
        return now()->addHours(12);
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
                ->allow(60) // Allow 60 jobs
                ->everyMinute() // In 60 seconds
                ->releaseAfterMinutes(60); // Release back to pool after 60 minutes

            return [$rateLimitedMiddleware];
        }

        return [];
    }

    /**
     * Handle a job failure.
     *
     * @param Throwable $exception
     * @return void
     */
    public function failed(Throwable $exception)
    {
        XCrawlerLog::create([
            'url' => Onejav::HOMEPAGE_URL . '/' . Carbon::now()->format(Onejav::DAILY_FORMAT),
            'payload' => [
                'message' => $exception->getMessage()
            ],
            'source' => OnejavService::SOURCE,
            'succeed' => false
        ]);
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
