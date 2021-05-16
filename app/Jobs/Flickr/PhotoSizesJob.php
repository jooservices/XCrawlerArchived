<?php

namespace App\Jobs\Flickr;

use App\Jobs\Traits\HasUnique;
use App\Models\FlickrPhoto;
use App\Services\Flickr\FlickrService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Get photo sizes
 * @package App\Jobs\Flickr
 */
class PhotoSizesJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use HasUnique;

    private FlickrPhoto $photo;

    public function __construct(FlickrPhoto $photo)
    {
        $this->photo = $photo;
    }

    /**
     * Determine the time at which the job should timeout.
     *
     * @return \DateTime
     */
    public function retryUntil()
    {
        return now()->addHours(6);
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
