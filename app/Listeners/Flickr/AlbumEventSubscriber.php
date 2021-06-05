<?php

namespace App\Listeners\Flickr;

use App\Events\Flickr\AlbumCreated;
use App\Events\Flickr\AlbumUpdated;
use App\Jobs\Flickr\AlbumPhotosJob;
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
             AlbumUpdated::class
        ], self::class . '@getAlbumPhotos');
    }
}
