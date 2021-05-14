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
            // Everything done than reset it for another loop
            FlickrContact::where(['state_code' => FlickrContact::STATE_PHOTOS_COMPLETED])->update(['state_code' => FlickrContact::STATE_INIT]);
            return;
        }

        ContactInfoJob::dispatch($contact);
    }
}
