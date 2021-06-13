<?php

namespace App\Flickr\Tests\Feature\Jobs;

use App\Flickr\Jobs\AlbumInfoJob;
use App\Flickr\Tests\AbstractFlickrTest;
use App\Models\FlickrAlbum;
use App\Models\FlickrContact;
use Illuminate\Support\Facades\Event;

class AlbumInfoJobTest extends AbstractFlickrTest
{
    public function test_can_get_album_info()
    {
        $this->mockSucceed();
        Event::fake();

        $album = FlickrAlbum::factory()->create([
            'id' => '72157646180569360',
            'owner' => FlickrContact::factory()->create([
                'nsid' => '94529704@N02'
            ])
        ]);

        AlbumInfoJob::dispatch($album->id, $album->owner);

        $album->refresh();
        $this->assertEquals(13, $album->photos);
        $this->assertEquals('Fake', $album->description);
    }

    public function test_cant_get_album_info()
    {
        $this->mockFailed();
        Event::fake();

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
