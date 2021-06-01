<?php

namespace Tests\Feature\Command\Flickr;

use App\Models\FlickrAlbum;
use App\Models\FlickrContact;
use Tests\AbstractFlickrTest;

class FlickrAlbumTest extends AbstractFlickrTest
{
    public function test_get_albums()
    {
        $this->mockSucceed();

        $contact = $this->factoryContact();
        $this->artisan('flickr:albums');
        $this->assertDatabaseCount('flickr_albums', 0);

        $contact->updateState(FlickrContact::STATE_PHOTOS_COMPLETED);
        $contact->refresh();

        $this->artisan('flickr:albums');
        $this->assertDatabaseCount('flickr_albums', 23);
        $this->assertEquals(23, FlickrAlbum::byState(FlickrAlbum::STATE_PHOTOS_COMPLETED)->count());
        $contact->refresh();
        $this->assertEquals(FlickrContact::STATE_ALBUM_COMPLETED, $contact->state_code);
    }
}
