<?php

namespace App\Console\Commands\Flickr;

use App\Jobs\Flickr\ContactInfoJob;
use App\Models\FlickrContact;
use Illuminate\Console\Command;

class ContactInfo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'flickr:contact-info';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get contact detail information';

    public function handle()
    {
        if (!$contact = FlickrContact::byState(FlickrContact::STATE_INIT)->first()) {
            return;
        }

        ContactInfoJob::dispatch($contact);
    }
}
