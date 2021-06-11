<?php

namespace App\Jobs\Flickr;

use App\Models\FlickrAlbum;
use App\Models\FlickrContact;
use App\Models\FlickrDownload;
use App\Models\FlickrDownloadItem;
use App\Models\FlickrPhoto;
use App\Services\Flickr\FlickrService;
use Carbon\Carbon;

class DownloadJob extends AbstractFlickrJob
{
    public FlickrDownload $download;
    public mixed $model;

    public function __construct(FlickrDownload $download)
    {
        $this->download = $download;
        $this->model = $download->model;
    }

    /**
     * The unique ID of the job.
     *
     * @return string
     */
    public function uniqueId(): string
    {
        if ($this->model instanceof FlickrAlbum) {
            return $this->getUnique([$this->model->id]);
        } elseif ($this->model instanceof FlickrContact) {
            return $this->getUnique([$this->model->nsid]);
        }

        return $this->getUnique([Carbon::now()]);
    }

    public function handle(FlickrService $service)
    {
        if ($this->model instanceof FlickrAlbum) {
            $this->downloadAlbum($service);
        }elseif ($this->model instanceof FlickrContact)
        {
            $this->downloadProfile($service);
        }
    }

    private function downloadAlbum(FlickrService $service)
    {
        if (!$photos = $service->getAlbumPhotos($this->model->id)) {
            $this->download->updateState(FlickrDownload::STATE_FAILED);
            return;
        }

        $photos->each(function ($photos) {
            foreach ($photos['photo'] as $photo) {
                // Create photos
                $photo = FlickrPhoto::updateOrCreate([
                    'id' => $photo['id'],
                    'owner' => $photos['owner'],
                ], [
                    'secret' => $photo['secret'],
                    'server' => $photo['server'],
                    'farm' => $photo['farm'],
                    'title' => $photo['title'],
                    'state_code' => FlickrPhoto::STATE_INIT
                ]);

                // Create download item
                FlickrDownloadItem::create([
                    'download_id' => $this->download->id,
                    'photo_id' => $photo->id,
                    'state_code' => $this->download->state_code === FlickrDownload::STATE_TO_WORDPRESS ? FlickrDownloadItem::STATE_WORDPRESS_INIT : FlickrDownloadItem::STATE_INIT
                ]);
            }
        });
    }

    private function downloadProfile(FlickrService $service)
    {
        $photos = $service->getAllPhotos($this->model->nsid);
        $photos->each(function ($page) {
            foreach ($page['photo'] as $photo) {
                // Create photos
                $photo = FlickrPhoto::updateOrCreate([
                    'id' => $photo['id'],
                    'owner' => $this->model->nsid,
                ], [
                    'secret' => $photo['secret'],
                    'server' => $photo['server'],
                    'farm' => $photo['farm'],
                    'title' => $photo['title'],
                    'state_code' => FlickrPhoto::STATE_INIT
                ]);

                // Create download item
                FlickrDownloadItem::create([
                    'download_id' => $this->download->id,
                    'photo_id' => $photo->id,
                    'state_code' => $this->download->state_code === FlickrDownload::STATE_TO_WORDPRESS ? FlickrDownloadItem::STATE_WORDPRESS_INIT : FlickrDownloadItem::STATE_INIT
                ]);
            }
        });
    }
}
