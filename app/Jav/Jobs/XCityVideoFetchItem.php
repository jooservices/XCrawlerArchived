<?php

namespace App\Jav\Jobs;

use App\Core\Jobs\Traits\HasUnique;
use App\Jav\Jobs\Traits\XCityJob;
use App\Models\TemporaryUrl;
use App\Models\XCityVideo;
use App\Services\Crawler\XCityVideoCrawler;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class XCityVideoFetchItem  implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use XCityJob;
    use HasUnique;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(private TemporaryUrl $url)
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
     * @return \DateTime
     */
    public function retryUntil()
    {
        return now()->addHours(6);
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
