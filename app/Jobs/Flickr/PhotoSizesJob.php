<?php

namespace App\Jobs\Flickr;

use App\Models\FlickrPhoto;
use App\Services\Flickr\FlickrService;

/**
 * Get photo sizes
 * @package App\Jobs\Flickr
 */
class PhotoSizesJob extends AbstractFlickrJob
{
    private FlickrPhoto $photo;

    public function __construct(FlickrPhoto $photo)
    {
        $this->photo = $photo;
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

    public function handle()
    {
        if (!$sizes = app(FlickrService::class)->getPhotoSize($this->photo->id)) {
            $this->photo->updateState(FlickrPhoto::STATE_SIZE_FAILED);
            return;
        }

        $this->photo->update(['sizes' => $sizes, 'state_code' => FlickrPhoto::STATE_SIZE_COMPLETED]);
    }
}
