<?php

namespace App\Console\Commands\Flickr;

use App\Jobs\Flickr\PhotoSizeJob;
use App\Models\FlickrPhoto;
use Illuminate\Console\Command;

class PhotoSize extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'flickr:photo-size';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch Flickr photos sizes';

    public function handle()
    {
        $photos = FlickrPhoto::whereNull('sizes')->limit(50)->get();
        foreach ($photos as $photo) {
            PhotoSizeJob::dispatch($photo);
        }
    }
}

