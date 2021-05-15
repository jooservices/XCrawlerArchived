<?php

namespace App\Jobs\Flickr;

use App\Models\FlickrContact;
use App\Models\FlickrPhoto;
use App\Services\FlickrService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Jooservices\PhpFlickr\FlickrException;
use Throwable;

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

    /**
     * The number of seconds after which the job's unique lock will be released.
     *
     * @var int
     */
    public int $uniqueFor = 900;

    private FlickrContact $contact;

    public function __construct(FlickrContact $contact)
    {
        $this->contact = $contact;
    }

    /**
     * Handle a job failure.
     *
     * @param Throwable $exception
     * @return void
     */
    public function failed(Throwable $exception)
    {

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
        return $this->contact->nsid;
    }

    public function handle()
    {
        $this->contact->updateState(FlickrContact::STATE_PHOTOS_PROCESSING);
        $service = app(FlickrService::class);
        try {
            $photos = $service->getAllPhotos($this->contact->nsid);
            $photos->each(function ($photos) {
                foreach ($photos['photo'] as $photo) {
                    FlickrPhoto::updateOrCreate([
                        'id' => $photo['id'],
                        'owner' => $photo['owner'],
                    ], array_merge($photo, ['state_code' => FlickrPhoto::STATE_INIT]));
                }
            });

            $this->contact->updateState(FlickrContact::STATE_PHOTOS_COMPLETED);
        }catch (FlickrException $exception)
        {
            $this->contact->updateState(FlickrContact::STATE_PHOTOS_FAILED);
            return;
        }
    }
}
