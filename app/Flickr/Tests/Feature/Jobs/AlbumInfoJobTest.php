<?php

namespace App\Flickr\Tests\Feature\Jobs;

use App\Flickr\Jobs\AlbumInfoJob;
use App\Flickr\Tests\AbstractFlickrTest;
use App\Models\FlickrAlbum;
use App\Models\FlickrContact;
use App\Services\Flickr\FlickrService;
use Illuminate\Support\Facades\Event;

class AlbumInfoJobTest extends AbstractFlickrTest
{
    public function setUp(): void
    {
        parent::setUp();
        Event::fake();
    }

    public function test_can_get_album_info()
    {
        $this->buildMock(true);
        $this->service = app(FlickrService::class);
        $album = FlickrAlbum::factory()->create([
            'id' => '72157692139427840',
            'owner' => FlickrContact::factory()->create([
                'nsid' => '94529704@N02'
            ])
        ]);

        AlbumInfoJob::dispatch($album->id, $album->owner);

        $album->refresh();
        $this->assertEquals(16, $album->photos);
        $this->assertEquals('Vy Trần - Are you 18+', $album->title);
    }

    public function test_cant_get_album_info()
    {
        $this->buildMock(false);
        $this->service = app(FlickrService::class);
        $album = FlickrAlbum::factory()->create([
            'id' => '72157646180569360',
            'owner' => FlickrContact::factory()->create([
                'nsid' => '94529704@N02'
            ])
        ]);

        AlbumInfoJob::dispatch($album->id, $album->owner);
        $this->assertEquals(FlickrAlbum::STATE_INFO_FAILED, $album->refresh()->state_code);
    }
}
