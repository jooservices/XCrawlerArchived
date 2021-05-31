<?php

namespace App\Jobs\Flickr;

use App\Models\FlickrAlbum;
use App\Models\FlickrPhoto;
use App\Services\Flickr\FlickrService;

/**
 * Get photos of album
 * @package App\Jobs\Flickr
 */
class AlbumPhotosJob extends AbstractFlickrJob
{
    private FlickrAlbum $album;

    public function __construct(FlickrAlbum $album)
    {
        $this->album = $album;
    }

    /**
     * The unique ID of the job.
     *
     * @return string
     */
    public function uniqueId(): string
    {
        return $this->getUnique([$this->album->id]);
    }

    public function handle()
    {
        $this->album->updateState(FlickrAlbum::STATE_PHOTOS_PROCESSING);
        $photos = app(FlickrService::class)->getAlbumPhotos($this->album->id);

        if ($photos->isEmpty()) {
            $this->album->updateState(FlickrAlbum::STATE_PHOTOS_FAILED);
            return;
        }

        $photos->each(function ($photos) {
            foreach ($photos['photo'] as $photo) {
                $photo = FlickrPhoto::firstOrCreate([
                    'id' => $photo['id'],
                    'owner' => $photos['owner']
                ], ['state_code' => FlickrPhoto::STATE_INIT]);

                $this->album->photos()->syncWithoutDetaching([$photo->id]);
            }
        });

        $this->album->updateState(FlickrAlbum::STATE_PHOTOS_COMPLETED);
    }
}
