<?php

namespace App\Console\Commands\Flickr;

use App\Jobs\Flickr\ContactAlbumbsJob;
use App\Models\FlickrContact;
use Illuminate\Console\Command;

class Albums extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'flickr:albums';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch Flickr Albums of user';

    public function handle()
    {
        if (!$contact = FlickrContact::byState(FlickrContact::STATE_PHOTOS_COMPLETED)->first()) {
            return;
        }

        ContactAlbumbsJob::dispatch($contact);
    }
}
