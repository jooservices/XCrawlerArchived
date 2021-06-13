<?php

namespace App\Flickr\Listeners;

use App\Flickr\Events\ItemDownloaded;
use App\Flickr\Mail\WordPressFlickrAlbumPost;
use App\Models\FlickrDownload;
use Illuminate\Support\Facades\Mail;

class DownloadItemSubscriber
{
    public function itemDownloaded(ItemDownloaded $event)
    {
        $download = $event->downloadItem->download;
        if (!$download->isCompleted()) {
            return;
        }

        // Completed
        if ($download->state_code === FlickrDownload::STATE_TO_WORDPRESS) {
            Mail::send(new WordPressFlickrAlbumPost($download));
        }

        $download->updateState(FlickrDownload::STATE_COMPLETED);
    }

    public function subscribe($events)
    {
        $events->listen([
            ItemDownloaded::class
        ], self::class . '@itemDownloaded');
    }
}
