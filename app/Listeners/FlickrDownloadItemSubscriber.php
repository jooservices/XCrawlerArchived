<?php

namespace App\Listeners;

use App\Events\Flickr\ItemDownloaded;
use App\Models\FlickrDownload;

class FlickrDownloadItemSubscriber
{
    public function itemDownloaded(ItemDownloaded $event)
    {
        $download = $event->downloadItem->download;
        if (!$download->isCompleted()) {
            return;
        }

        // Completed
        $download->updateState(FlickrDownload::STATE_COMPLETED);
    }

    public function subscribe($events)
    {
        $events->listen([
            ItemDownloaded::class
        ], self::class . '@itemDownloaded');
    }
}
