<?php

namespace Tests\Unit\Command\Flickr;

use App\Jobs\Flickr\PhotoSizesJob;
use App\Models\FlickrPhoto;
use Tests\AbstractFlickrTest;

class PhotoSizesTest extends AbstractFlickrTest
{
    public function test_get_photo_sizes()
    {
        $this->mockSucceed();
        $contact = $this->factoryContact();
        $photo = FlickrPhoto::factory()->create([
            'id' => '9472222272',
            'owner' => $contact->nsid
        ]);

        PhotoSizesJob::dispatch($photo);
        $photo->refresh();

        $this->assertEquals(FlickrPhoto::STATE_SIZE_COMPLETED, $photo->state_code);
        $this->assertIsArray($photo->sizes);
        $this->assertArrayHasKey('size', $photo->sizes);
    }

    public function test_cant_get_photo_sizes()
    {
        $contact = $this->factoryContact();
        $photo = FlickrPhoto::factory()->create([
            'id' => '9472222272',
            'owner' => $contact->nsid
        ]);

        PhotoSizesJob::dispatch($photo);
        $photo->refresh();

        $this->assertEquals(FlickrPhoto::STATE_SIZE_FAILED, $photo->state_code);
        $this->assertNull($photo->sizes);
    }
}
