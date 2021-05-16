<?php

namespace App\Jobs\Flickr;

use App\Jobs\Traits\HasUnique;
use App\Models\FlickrContact;
use App\Models\FlickrPhoto;
use App\Services\Flickr\FlickrService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Get photos of contact
 * @package App\Jobs\Flickr
 */
class PhotosJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use HasUnique;

    private FlickrContact $contact;

    public function __construct(FlickrContact $contact)
    {
        $this->contact = $contact;
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
        return $this->getUnique([$this->contact->nsid]);
    }

    public function handle()
    {
        $this->contact->updateState(FlickrContact::STATE_PHOTOS_PROCESSING);
        $photos = app(FlickrService::class)->getAllPhotos($this->contact->nsid);

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
