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

        /**
         * Get first contact with STATE_PHOTOS_COMPLETED
         * - So now we don't have any valid contact yet
         */
        $this->artisan('flickr:albums');
        $this->assertDatabaseCount('flickr_albums', 0);

        /**
         * This contact being valid
         */
        $contact->updateState(FlickrContact::STATE_PHOTOS_COMPLETED);
        $contact->refresh();

        /**
         * Call command again
         */
        $this->artisan('flickr:albums');
        $this->assertDatabaseCount('flickr_albums', 23);

        /**
         * Whenever new album created it'll trigger job to fetch photos
         * After fetch photos completed it'll update album state to STATE_PHOTOS_COMPLETED
         */
        $this->assertEquals(23, FlickrAlbum::byState(FlickrAlbum::STATE_PHOTOS_COMPLETED)->count());
        $contact->refresh();
        $this->assertEquals(FlickrContact::STATE_ALBUM_COMPLETED, $contact->state_code);
    }
}
