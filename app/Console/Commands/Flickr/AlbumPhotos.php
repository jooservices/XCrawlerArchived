<?php

namespace App\Console\Commands\Flickr;

use App\Jobs\Flickr\AlbumPhotosJob;
use App\Models\FlickrAlbum;
use Illuminate\Console\Command;

class AlbumPhotos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'flickr:album-photos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch Flickr Album';

    public function handle()
    {
        $album = FlickrAlbum::byState(FlickrAlbum::STATE_INIT)->first();
        if (!$album) {
            return;
        }
        AlbumPhotosJob::dispatch($album);
    }
}
