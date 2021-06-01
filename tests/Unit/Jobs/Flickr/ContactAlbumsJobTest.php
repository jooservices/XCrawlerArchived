<?php

namespace Tests\Unit\Jobs\Flickr;

use App\Jobs\Flickr\ContactAlbumbsJob;
use App\Models\FlickrContact;
use Tests\AbstractFlickrTest;

class ContactAlbumsJobTest extends AbstractFlickrTest
{
    public function test_can_get_albums()
    {
        $this->mockSucceed();
        $contact = $this->factoryContact();

        ContactAlbumbsJob::dispatch($contact);

        $this->assertDatabaseCount('flickr_albums', 23);
        $contact->refresh();
        $this->assertEquals(FlickrContact::STATE_ALBUM_COMPLETED, $contact->state_code);
    }

    public function test_cant_get_albums()
    {
        $this->mockFailed();

        $contact = $this->factoryContact();
        ContactAlbumbsJob::dispatch($contact);
        $this->assertDatabaseCount('flickr_albums', 0);
    }
}
