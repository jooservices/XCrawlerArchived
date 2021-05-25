<?php

namespace Tests\Feature\Command\Flickr;

use App\Jobs\Flickr\PhotosJob;
use App\Models\FlickrContact;
use Illuminate\Support\Facades\Queue;
use Tests\AbstractFlickrTest;

class PhotosTest extends AbstractFlickrTest
{
    public function test_get_photos()
    {
        $this->mockSucceed();
        $this->artisan('flickr:contacts');
        $this->assertDatabaseCount('flickr_contacts', 1070);
        $this->assertEquals(1070, FlickrContact::byState(FlickrContact::STATE_INIT)->count());

        $this->artisan('flickr:contact-info');
        $contact = FlickrContact::findByNsid('100028207@N03');
        $this->assertEquals(FlickrContact::STATE_INFO_COMPLETED, $contact->state_code);

        $this->artisan('flickr:photos');
        $this->assertDatabaseCount('flickr_photos', 6);
        $contact->refresh();
        $this->assertEquals(FlickrContact::STATE_PHOTOS_COMPLETED, $contact->state_code);
    }

    public function test_get_photos_failed()
    {
        $this->mockFailed();
        $contact = FlickrContact::factory()->create([
            'nsid' => '100028207@N03',
            'state_code' => FlickrContact::STATE_INFO_COMPLETED
        ]);

        $this->artisan('flickr:photos');
        $contact->refresh();

        $this->assertEquals(FlickrContact::STATE_PHOTOS_FAILED, $contact->state_code);
    }

    public function test_get_photos_with_no_photos()
    {
        Queue::fake();
        $this->artisan('flickr:photos');

        Queue::assertNotPushed(PhotosJob::class);

    }
}
