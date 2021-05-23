<?php

namespace Tests\Feature\Command\Flickr;

use App\Models\FlickrContact;
use Tests\AbstractFlickrTest;

class ContactsTest extends AbstractFlickrTest
{
    public function test_get_contacts()
    {
        $this->mockSucceed();
        $this->artisan('flickr:contacts');
        $this->assertDatabaseCount('flickr_contacts', 1070);
        $this->assertEquals(1070, FlickrContact::byState(FlickrContact::STATE_INIT)->count());
    }
}
