<?php

namespace App\Observers\Flickr;

use App\Jobs\Flickr\FlickrDownloadPhotoJob;
use App\Models\FlickrDownloadItem;

class DownloadItemObserver
{
    public function created(FlickrDownloadItem $downloadItem)
    {
        FlickrDownloadPhotoJob::dispatch($downloadItem);
    }
}
