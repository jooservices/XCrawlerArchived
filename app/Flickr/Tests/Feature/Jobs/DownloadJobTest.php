<?php

namespace App\Flickr\Tests\Feature\Jobs;

use App\Flickr\Jobs\DownloadJob;
use App\Flickr\Tests\AbstractFlickrTest;
use App\Models\FlickrDownload;
use App\Models\FlickrDownloadItem;
use App\Services\Flickr\FlickrService;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class DownloadJobTest extends AbstractFlickrTest
{
    public function setUp(): void
    {
        parent::setUp();
        Mail::fake();
        Storage::fake();

        $mock = $this->createMock(Client::class);
        $mock->method('get')->willReturn(new Response());
        app()->instance(Client::class, $mock);
    }

    public function test_can_download_album()
    {
        $this->buildMock(true);
        $this->service = app(FlickrService::class);

        DownloadJob::dispatch('https://www.flickr.com/photos/soulevilx/albums/72157692139427840', 'album', false);

        $this->assertDatabaseCount('flickr_albums', 1);
        $this->assertDatabaseCount('flickr_downloads', 1);

        $flickrDownload = FlickrDownload::first();
        $this->assertEquals(16, $flickrDownload->items->count());
        $this->assertDatabaseCount('flickr_photos', 16);
        $this->assertDatabaseHas('flickr_photos', ['id' => $flickrDownload->items->first()->photo_id]);

        $this->assertEquals(FlickrDownloadItem::STATE_COMPLETED, $flickrDownload->items()->first()->state_code);
        $this->assertEquals(FlickrDownload::STATE_COMPLETED, $flickrDownload->state_code);
    }

    public function test_can_download_profile()
    {
        $this->buildMock(true);
        $this->service = app(FlickrService::class);

        DownloadJob::dispatch('https://www.flickr.com/photos/soulevilx/albums/72157692139427840', 'profile', false);

        $this->assertDatabaseCount('flickr_photos', 358);
        $this->assertDatabaseCount('flickr_downloads', 1);
        $flickrDownload = FlickrDownload::first();

        $this->assertEquals(358, $flickrDownload->items->count());
        $this->assertEquals(FlickrDownloadItem::STATE_COMPLETED, $flickrDownload->items()->first()->state_code);
        $this->assertEquals(FlickrDownload::STATE_COMPLETED, $flickrDownload->state_code);
    }
}
