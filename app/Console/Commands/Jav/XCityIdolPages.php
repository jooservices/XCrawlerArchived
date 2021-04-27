<?php

namespace App\Console\Commands\Jav;

use App\Jobs\XCityIdolFetchPages;
use App\Services\Crawler\XCityIdolCrawler;
use Illuminate\Console\Command;

class XCityIdolPages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jav:xcity-idol-pages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get pages count of XCity idols';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $crawler = app(XCityIdolCrawler::class);
        $crawler->getSubPages()->each(function ($link) {
            XCityIdolFetchPages::dispatch($link);
        });
    }
}
