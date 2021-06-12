<?php

namespace App\Jobs\Flickr;

use App\Flickr\Events\ItemDownloaded;
use App\Models\FlickrDownloadItem;
use App\Services\Flickr\FlickrService;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;

class DownloadPhotoJob extends AbstractFlickrJob
{
    public FlickrDownloadItem $downloadItem;

    public function __construct(FlickrDownloadItem $downloadItem)
    {
        $this->downloadItem = $downloadItem;
    }

    /**
     * The unique ID of the job.
     *
     * @return string
     */
    public function uniqueId(): string
    {
        return $this->getUnique([$this->downloadItem->id]);
    }

    public function handle(FlickrService $service)
    {
        $photo = $this->downloadItem->photo;

        // Get sizes if don't have yet
        if (!$photo->hasSizes()) {
            $sizes = $service->getPhotoSize($photo->id);
            if (!$sizes) {
                $this->downloadItem->updateState(FlickrDownloadItem::STATE_FAILED);
                return;
            }
            $photo->update(['sizes' => $sizes]);
        }

        if ($this->downloadItem->state_code === FlickrDownloadItem::STATE_WORDPRESS_INIT) {
            $this->downloadItem->updateState(FlickrDownloadItem::STATE_COMPLETED);
            Event::dispatch(new ItemDownloaded($this->downloadItem));

            return;
        }

        $photoSize = $photo->largestSize();
        $downloadDir = $this->downloadItem->download->path;

        if (!Storage::makeDirectory($downloadDir)) {
            return;
        }

        $client = app(Client::class);
        $response = $client->get($photoSize['source'], [
            'sink' => storage_path('app/' . $downloadDir . '/' . basename($photoSize['source']))
        ]);

        if ($response->getStatusCode() !== 200) {
            $this->downloadItem->updateState(FlickrDownloadItem::STATE_FAILED);
            return;
        }

        $this->downloadItem->updateState(FlickrDownloadItem::STATE_COMPLETED);
        Event::dispatch(new ItemDownloaded($this->downloadItem));
    }
}
