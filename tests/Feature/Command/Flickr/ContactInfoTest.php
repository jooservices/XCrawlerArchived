<?php

namespace Tests\Feature\Command\Flickr;

use App\Events\Flickr\ContactCreated;
use App\Events\Flickr\ContactStateChanged;
use App\Models\FlickrContact;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Tests\AbstractFlickrTest;

class ContactInfoTest extends AbstractFlickrTest
{
    public function setUp(): void
    {
        parent::setUp();
        Event::fake([ContactCreated::class, ContactStateChanged::class]);
    }

    public function test_get_contact_info()
    {
        /**
         * From now whenever contact is created it will also trigger job to get detail info
         * We do fake event here to prevent that
         * Beside that this command used for relooping when all contact info are updated
         */
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
    }
}
