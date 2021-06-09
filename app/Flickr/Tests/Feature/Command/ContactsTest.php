<?php

namespace App\Flickr\Tests\Feature\Command;

use App\Events\Flickr\ContactCreated;
use App\Flickr\Tests\AbstractFlickrTest;
use App\Models\FlickrContact;
use Illuminate\Support\Facades\Event;

class ContactsTest extends AbstractFlickrTest
{
    public function setUp(): void
    {
        parent::setUp();
        Event::fake([ContactCreated::class]);
    }

    public function test_get_contacts()
    {
        $this->mockSucceed();

        // Won't be duplicated
        for ($index = 1; $index < 10; $index++) {
            $this->artisan('flickr:contacts');
        }

        $this->assertDatabaseCount('flickr_contacts', 1070);
        $this->assertEquals(1070, FlickrContact::byState(FlickrContact::STATE_INIT)->count());
    }
}
