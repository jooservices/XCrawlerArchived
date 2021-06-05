<?php

namespace App\Observer;

use App\Events\Flickr\AlbumCreated;
use App\Events\Flickr\AlbumUpdated;
use App\Models\FlickrAlbum;
use Illuminate\Support\Facades\Event;

class FlickrAlbumObserve
{
    public function created(FlickrAlbum $album)
    {
        Event::dispatch(new AlbumCreated($album));
    }

    public function updated(FlickrAlbum $album)
    {
        if (!$album->isDirty('state_code')) {
            return;
        }

        Event::dispatch(new AlbumUpdated($album));
    }
}
