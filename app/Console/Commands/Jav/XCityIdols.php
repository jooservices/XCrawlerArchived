<?php

namespace App\Console\Commands\Jav;

use App\Jobs\Jav\XCityIdolFetchItems;
use App\Services\Jav\XCityIdolService;
use App\Services\TemporaryUrlService;
use Illuminate\Console\Command;

class XCityIdols extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jav:xcity-idols';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get XCity idols links';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        /**
         * @var TemporaryUrlService $service
         */
        $service = app(TemporaryUrlService::class);

        // We have around 10 sub pages
        if ($service->getItems(XCityIdolService::SOURCE)->count() === 0) {
            app(XCityIdolService::class)->pages();
        }

        $service->getItems(XCityIdolService::SOURCE)->each(function ($page) {
            XCityIdolFetchItems::dispatch($page);
        });
    }
}
