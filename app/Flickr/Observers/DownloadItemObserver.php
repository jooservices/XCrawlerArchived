<?php

namespace App\Flickr\Observers;

use App\Flickr\Jobs\DownloadPhotoJob;
use App\Models\FlickrDownloadItem;

class DownloadItemObserver
{
    public function created(FlickrDownloadItem $downloadItem)
    {
        DownloadPhotoJob::dispatch($downloadItem);
    }
}
