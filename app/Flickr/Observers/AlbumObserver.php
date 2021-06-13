<?php

namespace App\Flickr\Observers;

use App\Flickr\Events\AlbumCreated;
use App\Flickr\Events\AlbumStateChanged;
use App\Models\FlickrAlbum;
use Illuminate\Support\Facades\Event;

class AlbumObserver
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

        Event::dispatch(new AlbumStateChanged($album));
    }
}
