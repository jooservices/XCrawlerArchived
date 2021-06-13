<?php

namespace App\Flickr\Tests\Feature\Jobs;

use App\Flickr\Jobs\ContactsJob;
use App\Flickr\Tests\AbstractFlickrTest;
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
        $this->assertDatabaseCount('flickr_contacts', self::TOTAL_CONTACTS);
        $this->assertEquals(1070, FlickrContact::byState(FlickrContact::STATE_INIT)->count());
    }

    public function test_do_not_duplicate_contacts()
    {
        /**
         * Fetch contacts and make sure it'll be inserted correctly
         */
        $this->mockSucceed();

        for ($index = 1; $index <= 10; $index++) {
            ContactsJob::dispatch();
        }

        $this->assertDatabaseCount('flickr_contacts', self::TOTAL_CONTACTS);
        $this->assertEquals(1070, FlickrContact::byState(FlickrContact::STATE_INIT)->count());
    }

    public function test_cant_get_contacts()
    {
        $this->mockFailed();

        ContactsJob::dispatch();
        $this->assertDatabaseCount('flickr_contacts', 0);
    }
}
