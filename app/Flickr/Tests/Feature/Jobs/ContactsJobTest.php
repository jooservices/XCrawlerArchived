<?php

namespace App\Flickr\Tests\Feature\Jobs;

use App\Flickr\Tests\AbstractFlickrTest;
use App\Jobs\Flickr\ContactsJob;
use App\Models\FlickrContact;
use Illuminate\Support\Facades\Event;

class ContactsJobTest extends AbstractFlickrTest
{
    public function setUp(): void
    {
        parent::setUp();
        Event::fake();
    }

    public function test_get_contacts()
    {
        /**
         * Fetch contacts and make sure it'll be inserted correctly
         */
        $this->mockSucceed();

        ContactsJob::dispatch();
        $this->assertDatabaseCount('flickr_contacts', 1070);
        $this->assertEquals(1070, FlickrContact::byState(FlickrContact::STATE_INIT)->count());
    }

    public function test_cant_get_contacts()
    {
        $this->mockFailed();

        ContactsJob::dispatch();
        $this->assertDatabaseCount('flickr_contacts', 0);
    }
}
