<?php

namespace Tests\Unit\Command\Flickr;

use App\Models\FlickrContact;

class PhotosTest extends AbstractFlickrTest
{
    public function test_get_photos()
    {
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
}
