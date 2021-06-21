<?php

namespace App\Jav\Jobs;

use App\Core\Jobs\Traits\HasUnique;
use App\Jav\Events\OnejavNewCompletedEvent;
use App\Models\Onejav;
use App\Models\TemporaryUrl;
use App\Services\Crawler\OnejavCrawler;
use DateTime;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Event;
use Spatie\RateLimitedMiddleware\RateLimited;

class OnejavFetchNewJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use HasUnique;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public TemporaryUrl $url)
    {
    }

    /**
     * The unique ID of the job.
     *
     * @return string
     */
    public function uniqueId(): string
    {
        return $this->getUnique([$this->url->url]);
    }

    /**
     * Determine the time at which the job should timeout.
     *
     * @return DateTime
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
                ->allow(4) // Allow 2 jobs
                ->everySecond() // In second
                ->releaseAfterMinutes(30); // Release back to pool after 30 minutes

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
        $currentPage = $this->url->data['current_page'] ?? 1;
        if ($currentPage === config('services.onejav.pages_count')) {
            $this->url->completed();
        } else {
            $currentPage++;
            $this->url->updateData(['current_page' => $currentPage]);
        }

        Event::dispatch(new OnejavNewCompletedEvent($this->url->refresh(), $items));
    }
}
