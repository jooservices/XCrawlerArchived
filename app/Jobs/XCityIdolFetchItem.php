<?php

namespace App\Jobs;

use App\Models\XCityIdol;
use App\Services\Crawler\XCityIdolCrawler;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\RateLimitedMiddleware\RateLimited;

class XCityIdolFetchItem implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of seconds after which the job's unique lock will be released.
     *
     * @var int
     */
    public int $uniqueFor = 1800;
    private XCityIdol $idol;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(XCityIdol $idol)
    {
        $this->idol = $idol;
    }

    /**
     * The unique ID of the job.
     *
     * @return string
     */
    public function uniqueId(): string
    {
        return $this->idol->url;
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

    public function handle()
    {
        $crawler = app(XCityIdolCrawler::class);
        $url = XCityIdol::HOMEPAGE_URL . $this->idol->url;

        // Get detail
        if ($item = $crawler->getItem($url)) {
            $data = $item->toArray();
            /**
             * By default url provided without host
             * We do not update again from item detail because its contained full url
             */
            unset($data['url']);
            $this->idol->update(array_merge($data, ['state_code' => XCityIdol::STATE_COMPLETED]));
        }
    }
}
