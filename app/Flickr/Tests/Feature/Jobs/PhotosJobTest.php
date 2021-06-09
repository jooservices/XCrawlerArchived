<?php

namespace App\Flickr\Tests\Feature\Jobs;

use App\Flickr\Tests\AbstractFlickrTest;
use App\Jobs\Flickr\PhotosJob;
use App\Models\FlickrContact;
use App\Models\FlickrPhoto;
use Illuminate\Support\Facades\Event;

class PhotosJobTest extends AbstractFlickrTest
{
    public function setUp(): void
    {
        parent::setUp();
        Event::fake();
    }

    public function test_can_get_photos()
    {
        $this->mockSucceed();
        $contact = $this->factoryContact();

        PhotosJob::dispatch($contact);
        $this->assertDatabaseCount('flickr_photos', 6);
        $this->assertEquals(6, FlickrPhoto::byState(FlickrPhoto::STATE_INIT)->count());
        $this->assertEquals(FlickrContact::STATE_PHOTOS_COMPLETED, $contact->refresh()->state_code);
    }

    public function test_cant_get_photos()
    {
        $this->mockFailed();
        $contact = $this->factoryContact();

        PhotosJob::dispatch($contact);
        $this->assertDatabaseCount('flickr_photos', 0);
        $this->assertEquals(FlickrContact::STATE_PHOTOS_FAILED, $contact->refresh()->state_code);
    }
}
