<?php

namespace App\Flickr\Tests\Feature\Command;

use App\Events\Flickr\ContactCreated;
use App\Events\Flickr\ContactStateChanged;
use App\Flickr\Tests\AbstractFlickrTest;
use App\Models\FlickrAlbum;
use App\Models\FlickrContact;
use Illuminate\Support\Facades\Event;

class AlbumPhotosTest extends AbstractFlickrTest
{
    public function setUp(): void
    {
        parent::setUp();
        Event::fake([ContactCreated::class, ContactStateChanged::class]);
    }

    public function test_can_get_album_photos()
    {
        $this->mockSucceed();
        $contact = FlickrContact::factory()->create([
            'nsid' => '94529704@N02'
        ]);
        $album = FlickrAlbum::factory()->create([
            'owner' => $contact->nsid
        ]);

        $this->artisan('flickr:album-photos');
        $this->assertDatabaseHas('flickr_photos', [
            'id' => '44472585915',
            'owner' => $contact->nsid
        ]);
        $this->assertDatabaseHas('flickr_photo_album', [
            'album_id' => $album->id,
            'photo_id' => '44472585915'
        ]);

        $this->assertEquals($contact->nsid, $album->owner()->first()->nsid);
        $this->assertEquals(FlickrContact::STATE_ALBUM_COMPLETED, $contact->refresh()->state_code);
    }
}
