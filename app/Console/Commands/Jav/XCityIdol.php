<?php

namespace App\Console\Commands\Jav;

use App\Jobs\XCityIdolFetchItem;
use App\Models\XCityIdol as XCityIdolModel;
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
        foreach (XCityIdolModel::forState(XCityIdolModel::STATE_INIT)->cursor() as $idol) {
            XCityIdolFetchItem::dispatch($idol);
        }
    }
}
