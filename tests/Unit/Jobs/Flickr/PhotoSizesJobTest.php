<?php

namespace Tests\Unit\Jobs\Flickr;

use App\Jobs\Flickr\PhotoSizesJob;
use App\Models\FlickrPhoto;
use Tests\AbstractFlickrTest;

class PhotoSizesJobTest extends AbstractFlickrTest
{
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
