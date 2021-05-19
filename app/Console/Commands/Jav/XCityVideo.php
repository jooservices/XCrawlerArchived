<?php

namespace App\Console\Commands\Jav;

use App\Jobs\Jav\XCityVideoFetchItem;
use App\Services\Jav\XCityVideoService;
use App\Services\TemporaryUrlService;
use Illuminate\Console\Command;

class XCityVideo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jav:xcity-video';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get XCity video detail';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $links = app(TemporaryUrlService::class)->getItems(XCityVideoService::SOURCE_VIDEO);
        foreach ($links as $link) {
            XCityVideoFetchItem::dispatch($link);
        }
    }
}
