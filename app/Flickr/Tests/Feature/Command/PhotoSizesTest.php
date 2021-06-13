<?php

namespace App\Flickr\Tests\Feature\Command;

use App\Flickr\Jobs\PhotoSizesJob;
use App\Flickr\Tests\AbstractFlickrTest;
use App\Models\FlickrPhoto;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;

class PhotoSizesTest extends AbstractFlickrTest
{
    public function setUp(): void
    {
        parent::setUp();
        Queue::fake();
        Event::fake();
    }

    public function test_get_photo_sizes()
    {
        $photo = FlickrPhoto::factory()->create();

        $this->artisan('flickr:photo-sizes');
        Queue::assertPushed(PhotoSizesJob::class, function ($event) use ($photo) {
            return $event->photo->id === $photo->id;
        });
    }

    public function test_cant_get_photo_sizes()
    {
        $this->artisan('flickr:photo-sizes');
        Queue::assertNothingPushed();
    }
}
