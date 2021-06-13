<?php

namespace App\Flickr\Tests\Unit\Observers;

use App\Flickr\Jobs\DownloadPhotoJob;
use App\Models\FlickrDownloadItem;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class DownloadItemObserverTest extends TestCase
{
    public function test_create_item_will_dispatch_download()
    {
        Queue::fake();
        $downloadItem = FlickrDownloadItem::factory()->create();

        Queue::assertPushed(DownloadPhotoJob::class, function ($event) use ($downloadItem) {
            return $event->downloadItem->id === $downloadItem->id;
        });
    }
}
