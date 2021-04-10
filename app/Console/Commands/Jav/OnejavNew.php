<?php

namespace App\Console\Commands\Jav;

use App\Jobs\OnejavFetchJob;
use App\Models\Onejav;
use App\Models\XCrawlerLog;
use Illuminate\Console\Command;

class OnejavNew extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jav:onejav-new {--page=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch Onejav - New';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $log = XCrawlerLog::filterSource('onejav.new')->first();
        $payload = optional($log)->payload;
        OnejavFetchJob::dispatch(Onejav::NEW_URL, isset($payload['page']) ? $payload['page'] + 1 : 1);
    }
}
