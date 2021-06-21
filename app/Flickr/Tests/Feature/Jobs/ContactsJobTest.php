<?php

namespace App\Flickr\Tests\Feature\Jobs;

use App\Flickr\Jobs\ContactsJob;
use App\Flickr\Tests\AbstractFlickrTest;
use App\Models\FlickrContact;
use App\Services\Flickr\FlickrService;
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
        $this->buildMock(true);
        $this->service = app(FlickrService::class);

        ContactsJob::dispatch();
        $this->assertDatabaseCount('flickr_contacts', self::TOTAL_CONTACTS);
        $this->assertEquals(self::TOTAL_CONTACTS, FlickrContact::byState(FlickrContact::STATE_INIT)->count());
    }

    public function test_do_not_duplicate_contacts()
    {
        /**
         * Fetch contacts and make sure it'll be inserted correctly
         */
        $this->buildMock(true);
        $this->service = app(FlickrService::class);

        for ($index = 1; $index <= 10; $index++) {
            ContactsJob::dispatch();
        }

        $this->assertDatabaseCount('flickr_contacts', self::TOTAL_CONTACTS);
        $this->assertEquals(self::TOTAL_CONTACTS, FlickrContact::byState(FlickrContact::STATE_INIT)->count());
    }

    public function test_cant_get_contacts()
    {
        $this->buildMock(false);
        $this->service = app(FlickrService::class);

        ContactsJob::dispatch();
        $this->assertDatabaseCount('flickr_contacts', 0);
    }
}
