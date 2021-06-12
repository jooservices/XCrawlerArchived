<?php

namespace App\Flickr\Tests\Feature\Jobs;

use App\Flickr\Tests\AbstractFlickrTest;
use App\Jobs\Flickr\DownloadJob;
use App\Models\FlickrAlbum;
use App\Models\FlickrContact;
use App\Models\FlickrDownload;
use App\Models\FlickrDownloadItem;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class DownloadJobTest extends AbstractFlickrTest
{
    public function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    public function test_can_download_album()
    {
        $this->mockSucceed();
        $contact = FlickrContact::factory()->create(['nsid' => '94529704@N02', 'state_code' => FlickrContact::STATE_MANUAL]);
        $album = FlickrAlbum::factory()->create(['owner' => $contact->nsid, 'photos' => 1]);

        $flickrDownload = FlickrDownload::create([
            'name' => $album->title,
            'path' => $album->owner . '/' . Str::slug($album->title),
            'total' => $album->photos,
            'model_id' => $album->id,
            'model_type' => FlickrAlbum::class,
            'state_code' => FlickrDownload::STATE_TO_WORDPRESS
        ]);

        DownloadJob::dispatch($flickrDownload);

        $this->assertEquals(1, $flickrDownload->items->count());
        $this->assertDatabaseCount('flickr_photos', 1);
        $this->assertDatabaseHas('flickr_photos', ['id' => $flickrDownload->items->first()->photo_id]);

        $flickrDownload->refresh();
        $this->assertEquals(FlickrDownloadItem::STATE_COMPLETED, $flickrDownload->items()->first()->state_code);
        $this->assertEquals(FlickrDownload::STATE_COMPLETED, $flickrDownload->state_code);
    }

    public function test_can_download_profile()
    {
        $this->mockSucceed();
        $contact = FlickrContact::factory()->create(['nsid' => '94529704@N02', 'state_code' => FlickrContact::STATE_MANUAL]);

        $flickrDownload = FlickrDownload::create([
            'name' => $contact->nsid,
            'path' => $contact->nsid,
            'total' => 1,
            'model_id' => $contact->nsid,
            'model_type' => FlickrContact::class,
            'state_code' => FlickrDownload::STATE_TO_WORDPRESS
        ]);

        DownloadJob::dispatch($flickrDownload);

        $this->assertEquals(6, $flickrDownload->items->count());
        $this->assertDatabaseCount('flickr_photos', 6);

        $flickrDownload->refresh();
        $this->assertEquals(FlickrDownloadItem::STATE_COMPLETED, $flickrDownload->items()->first()->state_code);
        $this->assertEquals(FlickrDownload::STATE_COMPLETED, $flickrDownload->state_code);
    }
}
