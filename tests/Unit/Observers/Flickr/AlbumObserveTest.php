<?php

namespace Tests\Unit\Observers\Flickr;

use App\Events\Flickr\AlbumCreated;
use App\Jobs\Flickr\AlbumPhotosJob;
use App\Models\FlickrAlbum;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class AlbumObserveTest extends TestCase
{
    public function test_create_flickr_album_will_dispatch_album_photos_job()
    {
        Queue::fake();

        /**
         * Whenever album is created we'll dispatch job to get photos
         */
        $album = FlickrAlbum::factory()->create();

        Queue::assertPushed(AlbumPhotosJob::class, function($job) use ($album) {
            return $job->album->id = $album->id;
        });
    }

    public function test_change_state_init_flickr_album_will_dispatch_album_photos_job()
    {
        Event::fake([AlbumCreated::class]);
        Queue::fake();

        $album = FlickrAlbum::factory()->create([
            'state_code' => FlickrAlbum::STATE_PHOTOS_COMPLETED
        ]);

        $album->updateState(FlickrAlbum::STATE_INIT);

        Queue::assertPushed(AlbumPhotosJob::class, function($job) use ($album) {
            return $job->album->id = $album->id;
        });
    }
}
