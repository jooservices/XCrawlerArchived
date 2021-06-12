<?php

namespace App\Flickr\Jobs;

use App\Models\FlickrContact;
use App\Models\FlickrPhoto;
use App\Services\Flickr\FlickrService;

class GetFavoritePhotosJob extends AbstractFlickrJob
{
    public FlickrContact $contact;

    public function __construct(FlickrContact $contact)
    {
        $this->contact = $contact;
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
        $photos = $service->getFavoritePhotos($this->contact->nsid);
        if (!$photos) {
            $this->contact->updateState(FlickrContact::STATE_INFO_FAILED);
            return;
        }

        $photos->each(function ($photos) use ($service) {
            foreach ($photos['photos']['photo'] as $photo) {
                /**
                 * Use STATE_MANUAL to prevent observer trigger
                 */
                FlickrContact::firstOrCreate([
                    'nsid' => $photo['owner'],
                ], ['state_code' => FlickrContact::STATE_MANUAL]);

                FlickrPhoto::firstOrCreate([
                    'id' => $photo['id'],
                    'owner' => $photo['owner']
                ], ['state_code' => FlickrPhoto::STATE_INIT]);
            }
        });
    }
}
