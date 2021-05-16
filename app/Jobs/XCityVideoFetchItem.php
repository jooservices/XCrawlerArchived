<?php

namespace App\Jobs;

use App\Jobs\Traits\XCityJob;
use App\Models\TemporaryUrl;
use App\Models\XCityVideo;
use App\Models\XCrawlerLog;
use App\Services\Crawler\XCityVideoCrawler;
use App\Services\Jav\XCityVideoService;
use Throwable;

class XCityVideoFetchItem extends AbstractUniqueUrlJob
{
    use XCityJob;

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
            'source' => XCityVideoService::SOURCE_VIDEO,
            'succeed' => false
        ]);
    }

    public function handle()
    {
        $crawler = app(XCityVideoCrawler::class);

        // Get detail
        if ($item = $crawler->getItem($this->url->url, $this->url->data['payload'])) {
            XCityVideo::firstOrCreate([
                'item_number' => $item->get('item_number')
            ], $item->toArray());

            $this->url->completed();

            return;
        }
        $this->url->update(['state_code' => TemporaryUrl::STATE_FAILED]);
    }
}
