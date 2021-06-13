<?php

namespace App\Flickr\Jobs;

use App\Models\FlickrContact;
use App\Services\Flickr\FlickrService;

/**
 * Get contact detail information
 * @package App\Flickr\Jobs\
 */
class ContactInfoJob extends AbstractFlickrJob
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
        if (!$contactInfo = $service->getPeopleInfo($this->contact->nsid)) {
            $this->contact->updateState(FlickrContact::STATE_INFO_FAILED);
            return;
        }

        $this->contact->update(array_merge($contactInfo['person'], ['state_code' => FlickrContact::STATE_INFO_COMPLETED]));
    }
}
