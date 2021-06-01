<?php

namespace App\Observer;

use App\Jobs\Flickr\ContactAlbumbsJob;
use App\Models\FlickrContact;

class FlickrContactObserve
{
    public function updated(FlickrContact $contact)
    {
        if (!$contact->isDirty('state_code'))
        {
            return;
        }

        if ($contact->state_code !== FlickrContact::STATE_PHOTOS_COMPLETED)
        {
            return;
        }

        ContactAlbumbsJob::dispatch($contact);
    }
}
