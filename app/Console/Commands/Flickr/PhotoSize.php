<?php

namespace App\Console\Commands\Flickr;

use App\Jobs\Flickr\PhotoSizesJob;
use App\Models\FlickrPhoto;
use Illuminate\Console\Command;

class PhotoSize extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'flickr:photo-sizes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch Flickr photos sizes';

    public function handle()
    {
        $photos = FlickrPhoto::byState(FlickrPhoto::STATE_INIT)->limit(40)->get();
        foreach ($photos as $photo) {
            PhotoSizesJob::dispatch($photo);
        }
    }
}

