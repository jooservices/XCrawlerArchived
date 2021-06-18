<?php

namespace App\Flickr\Jobs;

use App\Models\FlickrPhoto;
use App\Services\Flickr\FlickrService;

/**
 * Get photo sizes
 * @package App\Flickr\Jobs\
 */
class PhotoSizesJob extends AbstractFlickrJob
{
    public function __construct(public FlickrPhoto $photo)
    {
    }

    /**
     * The unique ID of the job.
     *
     * @return string
     */
    public function uniqueId(): string
    {
        return $this->getUnique([$this->photo->id]);
    }

    public function handle(FlickrService $service)
    {
        if (!$sizes = $service->getPhotoSize($this->photo->id)) {
            $this->photo->updateState(FlickrPhoto::STATE_SIZE_FAILED);
            return;
        }

        $this->photo->update(['sizes' => $sizes, 'state_code' => FlickrPhoto::STATE_SIZE_COMPLETED]);
    }
}
