<?php

namespace App\Flickr\Tests\Unit\Listeners;

use App\Events\Flickr\ContactCreated;
use App\Events\Flickr\ItemDownloaded;
use App\Flickr\Mail\WordPressFlickrAlbumPost;
use App\Flickr\Tests\AbstractFlickrTest;
use App\Models\FlickrDownload;
use App\Models\FlickrDownloadItem;
use App\Models\FlickrPhoto;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;

class ItemDownloadedTest extends AbstractFlickrTest
{
    public function setUp(): void
    {
        parent::setUp();
        Mail::fake();
        Event::fake([ContactCreated::class]);

        $this->mockSucceed();
        $mock = $this->createMock(Client::class);
        $mock->method('get')->willReturn(new Response());
        app()->instance(Client::class, $mock);
    }

    public function test_send_mail_after_item_downloaded()
    {
        $photo = FlickrPhoto::factory()->create();
        $download = FlickrDownload::factory()->create([
            'state_code' => FlickrDownload::STATE_TO_WORDPRESS,
            'total' => 1
        ]);
        $downloadItem = FlickrDownloadItem::factory()->create([
            'download_id' => $download->id,
            'photo_id' => $photo->id
        ]);

        Event::dispatch(new ItemDownloaded($downloadItem));
        Mail::assertQueued(WordPressFlickrAlbumPost::class);
        $download->refresh();
        $this->assertEquals(FlickrDownload::STATE_COMPLETED, $download->state_code);
    }

    public function test_do_not_send_mail_after_item_download_not_completed()
    {
        $photo = FlickrPhoto::factory()->create();
        $download = FlickrDownload::factory()->create([
            'state_code' => FlickrDownload::STATE_TO_WORDPRESS,
            'total' => 10
        ]);
        $downloadItem = FlickrDownloadItem::factory()->create([
            'download_id' => $download->id,
            'photo_id' => $photo->id
        ]);

        Event::dispatch(new ItemDownloaded($downloadItem));
        Mail::assertNothingQueued();
        $download->refresh();
        $this->assertEquals(FlickrDownload::STATE_TO_WORDPRESS, $download->state_code);
    }
}
