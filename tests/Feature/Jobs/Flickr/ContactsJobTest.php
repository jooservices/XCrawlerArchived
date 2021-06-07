<?php

namespace Tests\Feature\Jobs\Flickr;

use App\Events\Flickr\ContactCreated;
use App\Jobs\Flickr\ContactsJob;
use App\Models\FlickrContact;
use Illuminate\Support\Facades\Event;
use Tests\AbstractFlickrTest;

class ContactsJobTest extends AbstractFlickrTest
{
    public function setUp(): void
    {
        parent::setUp();
        Event::fake();
    }

    public function test_get_contacts()
    {
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
