<?php

namespace Tests\Feature\Command\Flickr;

use App\Models\FlickrContact;
use Illuminate\Support\Facades\DB;
use Tests\AbstractFlickrTest;

class ContactInfoTest extends AbstractFlickrTest
{
    public function test_get_contact_info()
    {
        $this->mockSucceed();
        $this->artisan('flickr:contacts');
        $this->assertDatabaseCount('flickr_contacts', 1070);
        $this->assertEquals(1070, FlickrContact::byState(FlickrContact::STATE_INIT)->count());

        $this->artisan('flickr:contact-info');
        $contact = FlickrContact::findByNsid('100028207@N03');
        $this->assertEquals(FlickrContact::STATE_INFO_COMPLETED, $contact->state_code);

        // Try to make all contacts are completed
        DB::table('flickr_contacts')->update(['state_code' => FlickrContact::STATE_PHOTOS_COMPLETED]);
        $this->assertEquals(0, FlickrContact::byState(FlickrContact::STATE_INIT)->count());

        // Execute contact-info again will reset back to INIT
        $this->artisan('flickr:contact-info'); // Reset everything back to INIT

        $contact = FlickrContact::findByNsid('100028207@N03');
        $this->assertEquals(FlickrContact::STATE_INIT, $contact->state_code);

        $this->artisan('flickr:contact-info');
        $contact = FlickrContact::findByNsid('100028207@N03');
        $this->assertEquals(FlickrContact::STATE_INFO_COMPLETED, $contact->state_code);
    }
}
