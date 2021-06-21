<?php

namespace App\Flickr\Tests\Feature\Jobs;

use App\Flickr\Events\ContactCreated;
use App\Flickr\Jobs\PhotoSizesJob;
use App\Flickr\Tests\AbstractFlickrTest;
use App\Models\FlickrPhoto;
use App\Services\Flickr\FlickrService;
use Illuminate\Support\Facades\Event;

class PhotoSizesJobTest extends AbstractFlickrTest
{
    public function setUp(): void
    {
        parent::setUp();
        Event::fake([ContactCreated::class]);
    }

    public function test_can_get_photo_sizes()
    {
        $this->buildMock(true);
        $this->service = app(FlickrService::class);
        $photo = FlickrPhoto::factory()->create();

        PhotoSizesJob::dispatch($photo);
        $photo->refresh();
        $this->assertIsArray($photo->sizes);
        $this->assertEquals(FlickrPhoto::STATE_SIZE_COMPLETED, $photo->state_code);
    }

    public function test_cant_get_photo_sizes()
    {
        $this->buildMock(false);
        $this->service = app(FlickrService::class);
        $photo = FlickrPhoto::factory()->create();

        PhotoSizesJob::dispatch($photo);
        $photo->refresh();
        $this->assertNull($photo->sizes);
        $this->assertEquals(FlickrPhoto::STATE_SIZE_FAILED, $photo->state_code);
    }
}
