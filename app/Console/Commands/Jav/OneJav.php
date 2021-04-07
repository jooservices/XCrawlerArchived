<?php

namespace App\Console\Commands\Jav;

use App\Jobs\OnejavFetch;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class OneJav extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jav:onejav {--page=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

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

        OnejavFetch::dispatch(\App\Models\Onejav::NEW_URL . '?page=' . $page);
        Cache::increment('onejav-news-page');
    }
}
