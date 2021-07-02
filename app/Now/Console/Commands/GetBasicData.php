<?php

namespace App\Now\Console\Commands;

use App\Now\Jobs\DeliverynowGetCollections;
use App\Now\Jobs\DeliverynowGetMetadata;
use App\Now\Jobs\TablenowGetMetadata;
use Illuminate\Console\Command;

class GetBasicData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'now:get-basic-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    public function handle()
    {
        DeliverynowGetMetadata::dispatch();
        //DeliverynowGetPromotions::dispatch();
        TablenowGetMetadata::dispatch();
        DeliverynowGetCollections::dispatch();
    }
}
