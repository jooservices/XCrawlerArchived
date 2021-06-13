<?php

namespace App\Flickr\Tests\Unit\Observers;

use App\Flickr\Jobs\AlbumPhotosJob;
use App\Models\FlickrAlbum;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class AlbumObserveTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Queue::fake();
    }

    public function test_create_flickr_album_will_dispatch_album_photos_job()
    {
        /**
         * Whenever album is created we'll dispatch job to get photos
         */
        $album = FlickrAlbum::factory()->create();
        Queue::assertPushed(AlbumPhotosJob::class, function ($job) use ($album) {
            return $job->album->id = $album->id;
        });
    }

    public function test_change_state_init_flickr_album_will_dispatch_album_photos_job()
    {
        $album = FlickrAlbum::factory()->create(['state_code' => FlickrAlbum::STATE_PHOTOS_COMPLETED]);
        $album->updateState(FlickrAlbum::STATE_INIT);
        Queue::assertPushed(AlbumPhotosJob::class, function ($job) use ($album) {
            return $job->album->id = $album->id;
        });
    }
}
