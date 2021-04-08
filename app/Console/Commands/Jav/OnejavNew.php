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
        $page = Cache::rememberForever('onejav-news-page', function () {
            return $this->option('page');
        });

        OnejavFetchJob::dispatch(Onejav::NEW_URL, $page);
        Cache::increment('onejav-news-page');
    }
}
