<?php

namespace Tests\Unit\Observers;

use App\Jobs\Flickr\AlbumPhotosJob;
use App\Models\FlickrAlbum;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class FlickrAlbumObserveTest extends TestCase
{
    public function test_create_flickr_album_will_dispatch_album_photos_job()
    {
        Queue::fake();

        FlickrAlbum::factory()->create();

        Queue::assertPushed(AlbumPhotosJob::class);
    }
}
