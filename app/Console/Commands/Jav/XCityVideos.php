<?php

namespace App\Console\Commands\Jav;

use App\Services\XCityVideoService;
use Illuminate\Console\Command;

class XCityVideos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jav:xcity-videos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get XCity videos links';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        app(XCityVideoService::class)->released();
    }
}
