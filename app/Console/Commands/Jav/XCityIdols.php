<?php

namespace App\Console\Commands\Jav;

use App\Jobs\XCityIdolFetchItems;
use App\Models\TemporaryUrl;
use App\Services\XCityIdolService;
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
        if (TemporaryUrl::forSource(XCityIdolService::SOURCE)->forState(TemporaryUrl::STATE_INIT)->count() === 0) {
            app(XCityIdolService::class)->pages();
        }

        foreach (TemporaryUrl::forSource(XCityIdolService::SOURCE)->forState(TemporaryUrl::STATE_INIT)->cursor() as $idolPage) {
            XCityIdolFetchItems::dispatch($idolPage);
        }
    }
}
