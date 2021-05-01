<?php

namespace App\Console\Commands\Jav;

use App\Jobs\R18FetchItemJob;
use App\Models\XCrawlerLog;
use App\Services\Crawler\R18Crawler;
use Illuminate\Console\Command;

class R18Released extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jav:r18-released';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch R18 - Release';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        /**
         * @TODO Use TemporaryUrls instead
         */
        $log = XCrawlerLog::filterSource('r18.released')->latest()->first();
        $payload = optional($log)->payload;
        $page = isset($payload['page']) ? $payload['page'] + 1 : 1;

        $crawler = app(R18Crawler::class);
        $url = 'https://www.r18.com/videos/vod/movies/list/pagesize=30/price=all/sort=new/type=all/page=' . $page;
        $links = $crawler->getItemLinks($url);

        $links->each(function ($link) {
            R18FetchItemJob::dispatch($link, 'r18.released');
        });

        XCrawlerLog::create([
            'url' => $url,
            'payload' => [
                'page' => $page,
                'count' => $links->count()
            ],
            'source' => 'r18.released',
            'succeed' => !$links->isEmpty()
        ]);
    }
}
