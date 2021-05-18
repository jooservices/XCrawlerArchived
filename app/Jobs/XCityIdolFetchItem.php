<?php

namespace App\Jobs;

use App\Jobs\Traits\HasUnique;
use App\Jobs\Traits\XCityJob;
use App\Models\Idol;
use App\Models\TemporaryUrl;
use App\Services\Crawler\XCityIdolCrawler;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class XCityIdolFetchItem implements ShouldQueue
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
