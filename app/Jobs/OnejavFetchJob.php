<?php

namespace App\Jobs;

use App\Models\Onejav;
use App\Models\XCrawlerLog;
use App\Services\Crawler\OnejavCrawler;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\RateLimitedMiddleware\RateLimited;

class OnejavFetchJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of seconds after which the job's unique lock will be released.
     *
     * @var int
     */
    public int $uniqueFor = 3600;
    public string $url;
    public int $page;
    public string $source;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $url, int $page = 1, string $source = 'new')
    {
        $this->url = $url;
        $this->page = $page;
        $this->source = $source;
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
        return now()->addDay();
    }

    /**
     * Attempt 1: Release after 60 seconds
     * Attempt 2: Release after 180 seconds
     * Attempt 3: Release after 420 seconds
     * Attempt 4: Release after 900 seconds
     */
    public function middleware()
    {
        $rateLimitedMiddleware = (new RateLimited())
            ->allow(60) // Allow 60 jobs
            ->everyMinute(1) // In 60 seconds
            ->releaseAfterMinutes(60); // Release back to pool after 60 minutes

        return [$rateLimitedMiddleware];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $crawler = app(OnejavCrawler::class);
        $payload = ['page' => $this->page];
        $items = $crawler->getItems($this->url, $payload);

        $items->each(function ($item) {
            Onejav::firstOrCreate(
                [
                    'url' => $item->get('url'),
                ],
                $item->toArray() + ['source' => $this->source]
            );
        });

        XCrawlerLog::create([
            'url' => $this->url,
            'payload' => array_merge_recursive(
                $payload,
                ['items' => $items->map(function ($item) {
                    return $item->get('url');
                })],
                ['count' => $items->count()]
            ),
            'source' => 'onejav.new',
            'succeed' => !$items->isEmpty()
        ]);
    }
}
