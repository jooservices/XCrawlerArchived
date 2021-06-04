<?php

namespace Tests\Unit\Jobs\Flickr;

use App\Models\FlickrDownload;
use App\Models\FlickrDownloadItem;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Tests\AbstractFlickrTest;

class FlickrDownloadItemJobTest extends AbstractFlickrTest
{
    public function test_download_item_succeed()
    {
        $mock = $this->createMock(Client::class);
        $mock->method('get')->willReturn(new Response());
        app()->instance(Client::class, $mock);
        $downloadItem = FlickrDownloadItem::factory()->create();

        $this->assertEquals(FlickrDownloadItem::STATE_COMPLETED, $downloadItem->refresh()->state_code);
        $this->assertEquals(FlickrDownload::STATE_COMPLETED, $downloadItem->download->state_code);
    }
}
