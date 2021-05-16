<?php

namespace App\Jobs\Flickr;

use App\Jobs\Traits\HasUnique;
use App\Models\FlickrContact;
use App\Services\Flickr\FlickrService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Get contact detail information
 * @package App\Jobs\Flickr
 */
class ContactInfoJob implements ShouldQueue, ShouldBeUnique
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
        if (!$contactInfo = app(FlickrService::class)->getPeopleInfo($this->contact->nsid)) {
            $this->contact->updateState(FlickrContact::STATE_INFO_FAILED);
            return;
        }

        $this->contact->update(array_merge($contactInfo['person'], ['state_code' => FlickrContact::STATE_INFO_COMPLETED]));
    }
}
