<?php

namespace App\Flickr\Tests\Feature\Jobs;

use App\Flickr\Jobs\ContactAlbumbsJob;
use App\Flickr\Tests\AbstractFlickrTest;
use App\Models\FlickrAlbum;
use App\Models\FlickrContact;
use App\Services\Flickr\FlickrService;
use Illuminate\Support\Facades\Event;

class ContactAlbumsJobTest extends AbstractFlickrTest
{
    public function setUp(): void
    {
        parent::setUp();
        Event::fake();
    }

    public function test_can_get_albums()
    {
        $this->buildMock(true);
        $this->service = app(FlickrService::class);
        $contact = $this->factoryContact();

        ContactAlbumbsJob::dispatch($contact);
        $this->assertDatabaseCount('flickr_albums', 23);
        $this->assertEquals(23, FlickrAlbum::byState(FlickrAlbum::STATE_INIT)->where(['owner' => $contact->nsid])->count());
        $this->assertEquals(FlickrContact::STATE_ALBUM_COMPLETED, $contact->refresh()->state_code);
    }

    public function test_cant_get_albums()
    {
        $this->buildMock(false);
        $this->service = app(FlickrService::class);
        $contact = $this->factoryContact();

        ContactAlbumbsJob::dispatch($contact);
        $this->assertDatabaseCount('flickr_albums', 0);
        $this->assertEquals(FlickrContact::STATE_ALBUM_FAILED, $contact->refresh()->state_code);
    }
}
