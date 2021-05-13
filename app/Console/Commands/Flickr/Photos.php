<?php

namespace App\Console\Commands\Flickr;

use App\Jobs\Flickr\PhotosJob;
use App\Models\FlickrContact;
use Illuminate\Console\Command;

class Photos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'flickr:photos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch Flickr photos of user';

    public function handle()
    {
        if (!$contact = FlickrContact::byState(FlickrContact::STATE_PEOPLE_INFO)->first()) {
            return;
        }

        PhotosJob::dispatch($contact);
    }
}

