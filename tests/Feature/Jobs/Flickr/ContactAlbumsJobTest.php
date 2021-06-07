<?php

namespace Tests\Feature\Jobs\Flickr;

use App\Jobs\Flickr\ContactAlbumbsJob;
use App\Models\FlickrContact;
use Illuminate\Support\Facades\Event;
use Tests\AbstractFlickrTest;

class ContactAlbumsJobTest extends AbstractFlickrTest
{
    public function setUp(): void
    {
        parent::setUp();
        Event::fake();
    }

    public function test_can_get_albums()
    {
        $this->mockSucceed();
        $contact = $this->factoryContact();

        ContactAlbumbsJob::dispatch($contact);
        $this->assertDatabaseCount('flickr_albums', 23);
        $this->assertEquals(FlickrContact::STATE_ALBUM_COMPLETED,  $contact->refresh()->state_code);
    }

    public function test_cant_get_albums()
    {
        $this->mockFailed();
        $contact = $this->factoryContact();

        ContactAlbumbsJob::dispatch($contact);
        $this->assertDatabaseCount('flickr_albums', 0);
        $this->assertEquals(FlickrContact::STATE_ALBUM_FAILED,  $contact->refresh()->state_code);
    }
}
