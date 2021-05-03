<?php

namespace App\Jobs;

use App\Models\Idol;
use App\Models\TemporaryUrl;
use App\Models\XCrawlerLog;
use App\Services\Crawler\XCityIdolCrawler;
use App\Services\XCityIdolService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\RateLimitedMiddleware\RateLimited;
use Throwable;

class XCityIdolFetchItem implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of seconds after which the job's unique lock will be released.
     *
     * @var int
     */
    public int $uniqueFor = 1800;
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
        return $this->url->url;
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
            ->allow(3) // Allow 3 jobs
            ->everySecond()
            ->releaseAfterSeconds(30); // Release back to pool after 30 seconds

        return [$rateLimitedMiddleware];
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
            'source' => XCityIdolService::SOURCE_IDOL,
            'succeed' => false
        ]);
    }

    public function handle()
    {
        $crawler = app(XCityIdolCrawler::class);

        // Get detail
        if ($item = $crawler->getItem($this->url->url)) {
            $name = $item->get('name');
            $pos = strpos($name, '[');

            if ($pos !== false) {
                $alias = trim(substr($name, $pos + 1), ']');
                $name = substr($name, 0, $pos);
            }

            $data = $item->toArray();
            $data['name'] = trim($name);
            $data['alias'] = isset($alias) ? explode(',', $alias) : null;

            /**
             * XCity have primary data for idol.
             * We are using updateOrCreate cos this reason
             */
            Idol::updateOrCreate(['name' => $data['name']], $data);
            $this->url->update(['state_code' => TemporaryUrl::STATE_COMPLETED]);

            return;
        }
        $this->url->update(['state_code' => TemporaryUrl::STATE_FAILED]);
    }
}
