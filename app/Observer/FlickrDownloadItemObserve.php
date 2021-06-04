<?php

namespace App\Observer;

use App\Jobs\Flickr\FlickrDownloadPhotoJob;
use App\Models\FlickrDownloadItem;

class FlickrDownloadItemObserve
{
    public function created(FlickrDownloadItem $downloadItem)
    {
        FlickrDownloadPhotoJob::dispatch($downloadItem);
    }
}
