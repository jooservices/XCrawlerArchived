<?php

namespace Tests\Unit\Jobs\Flickr;

use App\Jobs\Flickr\ContactInfoJob;
use App\Models\FlickrAlbum;
use App\Models\FlickrContact;
use Tests\AbstractFlickrTest;

class ContactAlbumsJobTest extends AbstractFlickrTest
{
    public function test_cant_get_albums()
    {
        $this->mockFailed();

        $contact = FlickrContact::factory()->create([
            'nsid' => '94529704@N02'
        ]);
        ContactInfoJob::dispatch($contact);
        $this->assertDatabaseCount('flickr_albums', 0);
    }

    public function test_can_get_albums()
    {
        $this->mockSucceed();
        $contact = FlickrContact::factory()->create([
            'nsid' => '94529704@N02',
            'state_code' => FlickrContact::STATE_PHOTOS_COMPLETED
        ]);

        $this->artisan('flickr:albums');
        $this->assertEquals(23, FlickrAlbum::byState(FlickrAlbum::STATE_INIT)->count());
        $contact->refresh();
        $this->assertEquals(FlickrContact::STATE_ALBUM_COMPLETED, $contact->state_code);
    }
}
