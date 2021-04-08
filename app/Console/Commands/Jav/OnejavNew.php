<?php

namespace App\Console\Commands\Jav;

use App\Jobs\OnejavFetchJob;
use App\Models\Onejav;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

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
        $page = round(Onejav::count() / 10, 0, PHP_ROUND_HALF_DOWN) + 1;
        OnejavFetchJob::dispatch(Onejav::NEW_URL, (int) $page);
    }
}
