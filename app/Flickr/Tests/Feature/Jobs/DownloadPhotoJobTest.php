<?php

namespace App\Flickr\Tests\Feature\Jobs;

use App\Flickr\Events\ContactCreated;
use App\Flickr\Tests\AbstractFlickrTest;
use App\Models\FlickrDownload;
use App\Models\FlickrDownloadItem;
use App\Models\FlickrPhoto;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Event;

class DownloadPhotoJobTest extends AbstractFlickrTest
{
    public function setUp(): void
    {
        parent::setUp();
        Event::fake([
            ContactCreated::class,
        ]);
    }

    public function test_download_item_succeed()
    {
        $mock = $this->createMock(Client::class);
        $mock->method('get')->willReturn(new Response());
        app()->instance(Client::class, $mock);
        /**
         * Observer will trigger job
         */
        $downloadItem = FlickrDownloadItem::factory()->create();

        $this->assertEquals(FlickrDownloadItem::STATE_COMPLETED, $downloadItem->refresh()->state_code);
        $this->assertEquals(FlickrDownload::STATE_COMPLETED, $downloadItem->download->state_code);
        $this->assertTrue($downloadItem->photo->hasSizes());
    }

    public function test_download_item_failed()
    {
        $mock = $this->createMock(Client::class);
        $mock->method('get')->willReturn(new Response(403));
        app()->instance(Client::class, $mock);
        /**
         * Observer will trigger job
         */
        $downloadItem = FlickrDownloadItem::factory()->create();

        $this->assertEquals(FlickrDownloadItem::STATE_FAILED, $downloadItem->refresh()->state_code);
        $this->assertTrue($downloadItem->photo->hasSizes());
    }

    public function test_download_item_have_no_sizes_yet()
    {
        // Mock getPhotoSize
        $this->mockSucceed();
        $mock = $this->createMock(Client::class);
        $mock->method('get')->willReturn(new Response());
        app()->instance(Client::class, $mock);

        /**
         * Observer will trigger job
         */
        $downloadItem = FlickrDownloadItem::factory()->create([
            'download_id' => FlickrDownload::factory()->create()->id,
            'photo_id' => FlickrPhoto::factory()->create([])->id,
            'state_code' => FlickrDownloadItem::STATE_INIT
        ]);

        $this->assertEquals(FlickrDownloadItem::STATE_COMPLETED, $downloadItem->refresh()->state_code);
        $this->assertEquals(FlickrDownload::STATE_COMPLETED, $downloadItem->download->state_code);
        $this->assertTrue($downloadItem->photo->hasSizes());
    }

    public function test_cant_download_item_have_no_sizes_yet()
    {
        $this->mockFailed();
        $mock = $this->createMock(Client::class);
        $mock->method('get')->willReturn(new Response());
        app()->instance(Client::class, $mock);

        /**
         * Observer will trigger job
         */
        $downloadItem = FlickrDownloadItem::factory()->create([
            'download_id' => FlickrDownload::factory()->create()->id,
            'photo_id' => FlickrPhoto::factory()->create([])->id,
            'state_code' => FlickrDownloadItem::STATE_INIT
        ]);

        $this->assertEquals(FlickrDownloadItem::STATE_FAILED, $downloadItem->refresh()->state_code);
        $this->assertFalse($downloadItem->photo->hasSizes());
    }
}
