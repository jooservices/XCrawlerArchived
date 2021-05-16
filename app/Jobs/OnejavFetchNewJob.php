<?php

namespace App\Jobs;

use App\Models\Onejav;
use App\Models\TemporaryUrl;
use App\Models\XCrawlerLog;
use App\Services\Crawler\OnejavCrawler;
use App\Services\OnejavService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\RateLimitedMiddleware\RateLimited;
use Throwable;

class OnejavFetchNewJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of seconds after which the job's unique lock will be released.
     *
     * @var int
     */
    public int $uniqueFor = 900;
    public TemporaryUrl $url;
    /**
     * @var OnejavCrawler|Application|mixed
     */
    private mixed $crawler;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(TemporaryUrl $url)
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
        return md5(serialize([$this->url->url, $this->url->source, $this->url->data, app()->environment('testing') ? Carbon::now() : null]));
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
            'url' => $this->url->url,
            'payload' => [
                'message' => $exception->getMessage(),
                'data' => $this->url->data,
            ],
            'source' => OnejavService::SOURCE,
            'succeed' => false
        ]);
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
                ->allow(60) // Allow 60 jobs
                ->everyMinute() // In 60 seconds
                ->releaseAfterMinutes(60); // Release back to pool after 60 minutes

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
        $items = $crawler->getItems($this->url->url, $this->url->data);
        $items->each(function ($item) {
            Onejav::firstOrCreate(['url' => $item->get('url')], $item->toArray());
        });

        // Onejav we can't get latest page without recursive
        $currentPage = (int)$this->url->data['current_page'];
        if ($currentPage === config('services.onejav.pages_count')) {
            $this->url->completed();
            return;
        }

        $currentPage++;
        $this->url->updateData(['current_page' => $currentPage]);
    }
}
