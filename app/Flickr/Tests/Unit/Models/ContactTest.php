<?php

namespace App\Flickr\Tests\Unit\Models;

use App\Models\FlickrAlbum;
use App\Models\FlickrContact;
use App\Models\FlickrPhoto;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class ContactTest extends TestCase
{
    /**
     * @var FlickrContact
     */
    private FlickrContact $contact;

    public function setUp(): void
    {
        parent::setUp();
        Event::fake();

        $this->contact = FlickrContact::factory()->create();
    }

    public function test_contact_photos_relationship()
    {
        $photo = FlickrPhoto::factory()->create(['owner' => $this->contact->nsid]);
        $photo2 = FlickrPhoto::factory()->create();

        $this->assertFalse($this->contact->photos()->where(['id' => $photo2->id])->exists());
        $this->assertEquals(1, $this->contact->photos()->count());
        $this->assertEquals($photo->id, $this->contact->photos()->first()->id);
    }

    public function test_contact_albums_relationship()
    {
        $album = FlickrAlbum::factory()->create(['owner' => $this->contact->nsid]);
        $album2 = FlickrAlbum::factory()->create();

        $this->assertFalse($this->contact->albums()->where(['id' => $album2->id])->exists());
        $this->assertEquals(1, $this->contact->albums()->count());
        $this->assertEquals($album->id, $this->contact->albums()->first()->id);
    }
}
