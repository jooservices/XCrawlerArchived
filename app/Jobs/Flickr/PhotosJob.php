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
use Throwable;

class PhotosJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

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
        $this->contact->update([
            'state_code' => FlickrContact::STATE_PHOTOS_FAILED
        ]);
    }
    /**
     * Determine the time at which the job should timeout.
     *
     * @return \DateTime
     */
    public function retryUntil()
    {
        return now()->addHours(12);
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
        $photos = $service->getAllPhotos($this->contact->nsid);
        $photos->each(function ($photos) {
            foreach ($photos['photo'] as $photo) {
                FlickrPhoto::updateOrCreate([
                    'id' => $photo['id'],
                    'owner' => $photo['owner'],
                ], $photo);
            }
        });

        $this->contact->updateState(FlickrContact::STATE_PHOTOS_COMPLETED);
    }
}
