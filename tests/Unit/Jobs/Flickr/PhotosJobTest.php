<?php

namespace Tests\Unit\Jobs\Flickr;

use App\Jobs\Flickr\PhotosJob;
use App\Models\FlickrContact;
use Tests\AbstractFlickrTest;

class PhotosJobTest extends AbstractFlickrTest
{
    public function test_can_get_photos()
    {
        $this->mockSucceed();
        $contact = $this->factoryContact();

        PhotosJob::dispatch($contact);
        $this->assertDatabaseCount('flickr_photos', 6);
        $contact->refresh();
        $this->assertEquals(FlickrContact::STATE_PHOTOS_COMPLETED, $contact->state_code);
    }

    public function test_cant_get_photos()
    {
        $this->mockFailed();
        $contact = $this->factoryContact();

        PhotosJob::dispatch($contact);
        $this->assertDatabaseCount('flickr_photos', 0);
        $contact->refresh();
        $this->assertEquals(FlickrContact::STATE_PHOTOS_FAILED, $contact->state_code);
    }
}
