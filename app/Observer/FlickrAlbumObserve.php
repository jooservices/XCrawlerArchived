<?php

namespace App\Observer;

use App\Jobs\Flickr\AlbumPhotosJob;
use App\Models\FlickrAlbum;

class FlickrAlbumObserve
{
    public function created(FlickrAlbum $album)
    {
        if ($album->state_code !== FlickrAlbum::STATE_INIT) {
            return;
        }

        AlbumPhotosJob::dispatch($album);
    }

    public function updated(FlickrAlbum $album)
    {
        if (!$album->isDirty('state_code')) {
            return;
        }

        if ($album->state_code !== FlickrAlbum::STATE_INIT) {
            return;
        }

        AlbumPhotosJob::dispatch($album);
    }
}
