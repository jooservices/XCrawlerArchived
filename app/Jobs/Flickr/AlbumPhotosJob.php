<?php

namespace App\Jobs\Flickr;

use App\Models\FlickrAlbum;
use App\Models\FlickrContact;
use App\Models\FlickrPhoto;
use App\Services\Flickr\FlickrService;

/**
 * Get photos of album
 * @package App\Jobs\Flickr
 */
class AlbumPhotosJob extends AbstractFlickrJob
{
    public FlickrAlbum $album;

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

    public function handle(FlickrService $service)
    {
        $this->album->updateState(FlickrAlbum::STATE_PHOTOS_PROCESSING);
        $photos = $service->getAlbumPhotos($this->album->id);

        if ($photos->isEmpty()) {
            $this->album->updateState(FlickrAlbum::STATE_PHOTOS_FAILED);
            return;
        }

        $photos->each(function ($photos) use ($service) {
            foreach ($photos['photo'] as $photo) {
                /**
                 * This job can process with not exists contact' album
                 * That's why we need create contact if not exists yet
                 * - Create contact if needed
                 * - Create photo
                 */
                FlickrContact::firstOrCreate([
                    'nsid' => $photos['owner'],
                ], ['state_code' => FlickrContact::STATE_MANUAL]);

                $photo = FlickrPhoto::firstOrCreate([
                    'id' => $photo['id'],
                    'owner' => $photos['owner']
                ], ['state_code' => FlickrPhoto::STATE_INIT]);

                $this->album->photos()->syncWithoutDetaching([$photo->id]);
            }
        });

        $this->album->updateState(FlickrAlbum::STATE_PHOTOS_COMPLETED);
        $contact = $this->album->owner()->first();

        if ($contact->state_code === FlickrContact::STATE_INIT) {
            $contact->updateState(FlickrContact::STATE_ALBUM_COMPLETED);
        }
    }
}
