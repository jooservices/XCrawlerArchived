<?php

namespace App\Jobs;

use App\Models\R18;
use App\Models\XCrawlerLog;
use App\Services\Crawler\R18Crawler;
use App\Services\R18Service;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\RateLimitedMiddleware\RateLimited;
use Throwable;

class R18FetchItemJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of seconds after which the job's unique lock will be released.
     *
     * @var int
     */
    public int $uniqueFor = 1800;
    public string $url;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $url)
    {
        $this->url = $url;
    }

    /**
     * The unique ID of the job.
     *
     * @return string
     */
    public function uniqueId(): string
    {
        return $this->url;
    }

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
                ->allow(6) // Allow 6 jobs
                ->everySecond()
                ->releaseAfterSeconds(5); // Release back to pool after 5 seconds

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
            'url' => $this->url,
            'payload' => [
                'message' => $exception->getMessage()
            ],
            'source' => R18Service::SOURCE,
            'succeed' => false
        ]);
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \Exception
     */
    public function handle()
    {
        $crawler = app(R18Crawler::class);
        if (!$item = $crawler->getItem($this->url)) {
            XCrawlerLog::create([
                'url' => $this->url,
                'payload' => [],
                'source' => 'r18.item',
                'succeed' => false
            ]);

            return;
        }

        R18::firstOrCreate(['url' => $item->get('url'),], $item->toArray());
    }
}
