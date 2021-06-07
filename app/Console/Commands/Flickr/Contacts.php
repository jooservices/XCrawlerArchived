<?php

namespace App\Console\Commands\Flickr;

use App\Jobs\Flickr\ContactsJob;
use App\Models\FlickrContact;
use Illuminate\Console\Command;

class Contacts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'flickr:contacts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get contacts of authorized user';

    public function handle()
    {
        ContactsJob::dispatch();
    }
}
