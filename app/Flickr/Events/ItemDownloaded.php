<?php

namespace App\Flickr\Events;

use App\Models\FlickrDownloadItem;

class ItemDownloaded
{
    public function __construct(public FlickrDownloadItem $downloadItem)
    {
    }
}
