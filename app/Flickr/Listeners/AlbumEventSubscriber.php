<?php

namespace App\Flickr\Listeners;

use App\Flickr\Events\AlbumCreated;
use App\Flickr\Events\AlbumStateChanged;
use App\Flickr\Jobs\AlbumPhotosJob;
use App\Models\FlickrAlbum;

class AlbumEventSubscriber
{
    public function getAlbumPhotos($event)
    {
        if ($event->album->state_code !== FlickrAlbum::STATE_INIT) {
            return;
        }

        AlbumPhotosJob::dispatch($event->album);
    }

    public function subscribe($events)
    {
        $events->listen([
            AlbumCreated::class,
            AlbumStateChanged::class
        ], self::class . '@getAlbumPhotos');
    }
}
