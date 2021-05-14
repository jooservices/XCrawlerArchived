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
    protected $description = 'Get photos of a contact';

    public function handle()
    {
        if (!$contact = FlickrContact::byState(FlickrContact::STATE_INFO_COMPLETED)->first()) {
            return;
        }

        PhotosJob::dispatch($contact);
    }
}

