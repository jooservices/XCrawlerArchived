<?php

namespace App\Jobs;

use App\Models\XCityIdol;
use App\Models\XCityIdolPage;
use App\Services\Crawler\XCityIdolCrawler;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\RateLimitedMiddleware\RateLimited;

class XCityIdolFetchItems implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of seconds after which the job's unique lock will be released.
     *
     * @var int
     */
    public int $uniqueFor = 1800;
    private XCityIdolPage $page;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(XCityIdolPage $page)
    {
        $this->page = $page;
    }

    /**
     * The unique ID of the job.
     *
     * @return string
     */
    public function uniqueId(): string
    {
        return $this->page->url;
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
        $url = XCityIdol::ENDPOINT_URL . $this->page->url . '&num=' . XCityIdol::PER_PAGE . '&page=' . $this->page->current + 1;

        // Get detail
        $crawler->getItemLinks($url)->each(function ($link) {
            XCityIdol::firstOrCreate(['url' => $link], ['state_code' => XCityIdol::STATE_INIT]);
        });

        $currentPage = $this->page->current + 1;
        if ($currentPage > $this->page->pages) {
            $this->page->update(['current' => 0]);

            return;
        }

        $this->page->increment('current');
    }
}
