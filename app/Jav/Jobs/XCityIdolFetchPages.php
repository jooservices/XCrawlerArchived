<?php

namespace App\Jav\Jobs;

use App\Jobs\Traits\HasUnique;
use App\Jav\Jobs\Traits\XCityJob;
use App\Models\TemporaryUrl;
use App\Models\XCityIdol;
use App\Services\Crawler\XCityIdolCrawler;
use App\Services\Jav\XCityIdolService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class XCityIdolFetchPages implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use XCityJob;
    use HasUnique;

    public string $url;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $url)
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
        return $this->getUnique([$this->url]);
    }

    public function handle()
    {
        $crawler = app(XCityIdolCrawler::class);
        $url = XCityIdol::ENDPOINT_URL . trim($this->url);
        $query = parse_url($url, PHP_URL_QUERY);
        $url = str_replace('?' . $query, '', $url);
        parse_str($query, $query);
        $query['num'] = XCityIdol::PER_PAGE;

        if ($pages = $crawler->getPages($url, $query)) {
            TemporaryUrl::firstOrCreate([
                'url' => $url,
                'source' => XCityIdolService::SOURCE,
                'data' => [
                    'pages' => $pages,
                    'current_page' => 1,
                    'payload' => $query,
                ],
                'state_code' => TemporaryUrl::STATE_INIT
            ]);
        }
    }
}
