<?php

namespace Tests\Unit\Observers\Flickr;

use App\Jobs\Flickr\AlbumPhotosJob;
use App\Models\FlickrAlbum;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class FlickrAlbumObserveTest extends TestCase
{
    public function test_create_flickr_album_will_dispatch_album_photos_job()
    {
        Queue::fake();

        /**
         * Whenever album is created we'll dispatch job to get photos
         */
        FlickrAlbum::factory()->create();

        Queue::assertPushed(AlbumPhotosJob::class);
    }
}
