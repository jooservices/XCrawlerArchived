<?php

namespace App\Flickr\Tests\Feature\Command;

use App\Flickr\Tests\AbstractFlickrTest;
use App\Jobs\Flickr\AlbumPhotosJob;
use App\Models\FlickrAlbum;
use App\Models\FlickrContact;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;

class AlbumPhotosTest extends AbstractFlickrTest
{
    public function setUp(): void
    {
        parent::setUp();
        Queue::fake();
        Event::fake();
    }

    public function test_can_get_album_photos()
    {
        $this->mockSucceed();

        $contact = FlickrContact::factory()->create(['nsid' => '94529704@N02']);
        $album = FlickrAlbum::factory()->create(['owner' => $contact->nsid]);

        $this->artisan('flickr:album-photos');
        Queue::assertPushed(AlbumPhotosJob::class, function ($event) use ($album) {
            return $event->album->id === $album->id;
        });
    }

    public function test_cant_get_album_photos()
    {
        $this->mockSucceed();

        $contact = FlickrContact::factory()->create(['nsid' => '94529704@N02']);
        $album = FlickrAlbum::factory()->create(['owner' => $contact->nsid, 'state_code' => FlickrAlbum::STATE_PHOTOS_COMPLETED]);

        $this->artisan('flickr:album-photos');
        Queue::assertNotPushed(AlbumPhotosJob::class, function ($event) use ($album) {
            return $event->album->id === $album->id;
        });
    }
}
