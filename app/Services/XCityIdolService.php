<?php

namespace App\Services;

use App\Jobs\XCityIdolFetchPages;
use App\Services\Crawler\XCityIdolCrawler;

class XCityIdolService
{
    public const SOURCE = 'xcity_idols';
    public const SOURCE_IDOL = 'xcity_idol';

    public function pages()
    {
        $crawler = app(XCityIdolCrawler::class);
        $crawler->getSubPages()->each(function ($link) {
            XCityIdolFetchPages::dispatch($link);
        });
    }
}
