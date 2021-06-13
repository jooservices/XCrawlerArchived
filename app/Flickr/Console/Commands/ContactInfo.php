<?php

namespace App\Flickr\Console\Commands;

use App\Flickr\Jobs\ContactInfoJob;
use App\Flickr\Jobs\GetFavoritePhotosJob;
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
        if (!$contact = FlickrContact::whereIn('state_code', [FlickrContact::STATE_INIT, FlickrContact::STATE_MANUAL])->first()) {
            return;
        }

        ContactInfoJob::dispatch($contact);
        GetFavoritePhotosJob::dispatch($contact);
    }
}
