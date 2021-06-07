<?php

namespace App\Jobs\Jav;

use App\Jobs\Traits\HasUnique;
use App\Jobs\Traits\XCityJob;
use App\Models\TemporaryUrl;
use App\Models\XCityIdol;
use App\Services\Crawler\XCityIdolCrawler;
use App\Services\Jav\XCityIdolService;
use App\Services\TemporaryUrlService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class XCityIdolFetchItems implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use XCityJob;
    use HasUnique;

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
        return $this->getUnique([$this->url->url]);
    }

    /**
     * Determine the time at which the job should timeout.
     *
     * @return \DateTime
     */
    public function retryUntil()
    {
        return now()->addHours(6);
    }

    public function handle()
    {
        $crawler = app(XCityIdolCrawler::class);
        $service = app(TemporaryUrlService::class);

        $currentPage = $this->url->data['current_page'] ?? 1;
        $payload = $this->url->data['payload'];
        $payload['url'] = $this->url->url;
        $payload['page'] = $currentPage;


        /**
         * Get idols on page
         * We have around 30 idols / page
         */
        $crawler->getItemLinks($this->url->url, $payload)->each(function ($link) use ($service, $payload) {;
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
