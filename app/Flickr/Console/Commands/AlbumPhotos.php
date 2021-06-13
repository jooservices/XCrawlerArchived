<?php

namespace App\Flickr\Console\Commands;

use App\Flickr\Jobs\AlbumPhotosJob;
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
    protected $description = 'Fetch Album photos';

    public function handle()
    {
        if (!$album = FlickrAlbum::whereIn('state_code', [FlickrAlbum::STATE_INIT, FlickrAlbum::STATE_MANUAL])->first()) {
            return;
        }
        AlbumPhotosJob::dispatch($album);
    }
}
