<?php

namespace App\Jobs;

use App\Jobs\Traits\XCityJob;
use App\Models\Idol;
use App\Models\TemporaryUrl;
use App\Models\XCrawlerLog;
use App\Services\Crawler\XCityIdolCrawler;
use App\Services\XCityIdolService;
use Throwable;

class XCityIdolFetchItem extends AbstractUniqueUrlJob
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
