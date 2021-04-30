<?php

namespace App\Console\Commands\Jav;

use App\Jobs\XCityIdolFetchItems;
use App\Models\XCityIdolPage;
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
        foreach (XCityIdolPage::cursor() as $idolPage) {
            XCityIdolFetchItems::dispatch($idolPage);
        }
    }
}
