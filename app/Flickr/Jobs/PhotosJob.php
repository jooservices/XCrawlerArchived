<?php

namespace App\Flickr\Jobs;

use App\Models\FlickrContact;
use App\Models\FlickrPhoto;
use App\Services\Flickr\FlickrService;

/**
 * Get photos of contact
 * @package App\Flickr\Jobs\
 */
class PhotosJob extends AbstractFlickrJob
{
    public function __construct(public FlickrContact $contact)
    {
    }

    /**
     * The unique ID of the job.
     *
     * @return string
     */
    public function uniqueId(): string
    {
        return $this->getUnique([$this->contact->nsid]);
    }

    public function handle(FlickrService $service)
    {
        $this->contact->updateState(FlickrContact::STATE_PHOTOS_PROCESSING);
        $photos = $service->getAllPhotos($this->contact->nsid);

        if ($photos->isEmpty()) {
            $this->contact->updateState(FlickrContact::STATE_PHOTOS_FAILED);
            return;
        }

        $photos->each(function ($photos) {
            foreach ($photos['photo'] as $photo) {
                FlickrPhoto::updateOrCreate([
                    'id' => $photo['id'],
                    'owner' => $photo['owner'],
                ], array_merge($photo, ['state_code' => FlickrPhoto::STATE_INIT]));
            }
        });

        $this->contact->updateState(FlickrContact::STATE_PHOTOS_COMPLETED);
    }
}
