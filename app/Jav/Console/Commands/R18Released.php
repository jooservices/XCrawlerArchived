<?php

namespace App\Jav\Console\Commands;

use App\Services\Jav\R18Service;
use Illuminate\Console\Command;

class R18Released extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jav:r18-released';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch R18 - Release';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        app(R18Service::class)->released();
    }
}
