<?php

namespace App\Console\Commands\Jav;

use App\Jobs\XCityIdolFetchItem;
use App\Services\TemporaryUrlService;
use Illuminate\Console\Command;

class XCityIdol extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jav:xcity-idol';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get XCity idol detail';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $links = app(TemporaryUrlService::class)->getItems('xcity.idol');
        foreach ($links as $link) {
            XCityIdolFetchItem::dispatch($link);
        }
    }
}
