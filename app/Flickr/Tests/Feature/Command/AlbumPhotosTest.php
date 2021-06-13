<?php

namespace App\Flickr\Tests\Feature\Command;

use App\Flickr\Jobs\AlbumPhotosJob;
use App\Flickr\Tests\AbstractFlickrTest;
use App\Models\FlickrAlbum;
use App\Models\FlickrContact;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;

class AlbumPhotosTest extends AbstractFlickrTest
{
    private FlickrContact $contact;
    private FlickrAlbum $album;

    public function setUp(): void
    {
        parent::setUp();
        Queue::fake();
        Event::fake();

        $this->contact = FlickrContact::factory()->create(['nsid' => '94529704@N02']);
        $this->album = FlickrAlbum::factory()->create(['owner' => $this->contact->nsid]);
    }

    public function test_can_get_album_photos()
    {
        $this->artisan('flickr:album-photos');
        Queue::assertPushed(AlbumPhotosJob::class, function ($event){
            return $event->album->id === $this->album->id;
        });
    }

    public function test_cant_get_album_photos()
    {
        $this->album->updateState(FlickrAlbum::STATE_PHOTOS_COMPLETED);
        $this->artisan('flickr:album-photos');
        Queue::assertNotPushed(AlbumPhotosJob::class, function ($event) {
            return $event->album->id === $this->album->id;
        });
    }
}
