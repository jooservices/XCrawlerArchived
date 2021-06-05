<?php

namespace Tests\Unit\Jobs\Flickr;

use App\Events\Flickr\ContactCreated;
use App\Jobs\Flickr\PhotoSizesJob;
use App\Models\FlickrPhoto;
use Illuminate\Support\Facades\Event;
use Tests\AbstractFlickrTest;

class PhotoSizesJobTest extends AbstractFlickrTest
{
    public function setUp(): void
    {
        parent::setUp();
        Event::fake([ContactCreated::class]);
    }

    public function test_can_get_photo_sizes()
    {
        $this->mockSucceed();
        $photo = FlickrPhoto::factory()->create();

        PhotoSizesJob::dispatch($photo);
        $photo->refresh();
        $this->assertIsArray($photo->sizes);
        $this->assertEquals(FlickrPhoto::STATE_SIZE_COMPLETED, $photo->state_code);
    }

    public function test_cant_get_photo_sizes()
    {
        $this->mockFailed();
        $photo = FlickrPhoto::factory()->create();

        PhotoSizesJob::dispatch($photo);
        $photo->refresh();
        $this->assertNull($photo->sizes);
        $this->assertEquals(FlickrPhoto::STATE_SIZE_FAILED, $photo->state_code);
    }
}
