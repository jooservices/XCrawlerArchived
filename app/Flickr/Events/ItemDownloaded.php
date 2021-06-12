<?php

namespace App\Flickr\Events;

use App\Models\FlickrDownloadItem;

class ItemDownloaded
{
    public FlickrDownloadItem $downloadItem;

    public function __construct(FlickrDownloadItem $downloadItem)
    {
        $this->downloadItem = $downloadItem;
    }
}
