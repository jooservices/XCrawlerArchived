<?php

namespace App\Jobs;

use App\Models\TemporaryUrl;
use App\Models\XCityIdol;
use App\Models\XCrawlerLog;
use App\Services\Crawler\XCityIdolCrawler;
use App\Services\TemporaryUrlService;
use App\Services\XCityIdolService;
use App\Services\XCityVideoService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\RateLimitedMiddleware\RateLimited;
use Throwable;

class XCityIdolFetchItems implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of seconds after which the job's unique lock will be released.
     *
     * @var int
     */
    public int $uniqueFor = 900;
    private TemporaryUrl $url;


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
        // This URL using payload
        return md5(serialize([$this->url->url, $this->url->source, $this->url->data, app()->environment('testing') ? Carbon::now() : null]));
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
        if (config('app.env') !== 'testing') {
            $rateLimitedMiddleware = (new RateLimited())
                ->allow(3) // Allow 3 jobs
                ->everySecond()
                ->releaseAfterSeconds(30); // Release back to pool after 30 seconds

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
            'url' => $this->url->url,
            'payload' => [
                'message' => $exception->getMessage(),
                'data' => $this->url->data,
            ],
            'source' => XCityVideoService::SOURCE,
            'succeed' => false
        ]);
    }

    public function handle()
    {
        $crawler = app(XCityIdolCrawler::class);
        $service = app(TemporaryUrlService::class);

        $currentPage = $this->url->data['current_page'];
        $payload = $this->url->data['payload'];
        $payload['url'] = $this->url->url;
        $payload['page'] = $currentPage;

        /**
         * Get idols on page
         * We have around 30 idols / page
         */
        $crawler->getItemLinks($this->url->url, $payload)->each(function ($link) use ($service, $payload) {
            $service->create(XCityIdol::HOMEPAGE_URL . $link, XCityIdolService::SOURCE_IDOL, $payload);
        });

        if ($currentPage === (int)$this->url->data['pages']) {
            $this->url->completed();

            return;
        }

        $currentPage++;
        $this->url->updateData(['current_page' => $currentPage]);
    }
}
