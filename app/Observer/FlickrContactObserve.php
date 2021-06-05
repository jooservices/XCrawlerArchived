<?php

namespace App\Observer;

use App\Events\Flickr\ContactCreated;
use App\Jobs\Flickr\ContactAlbumbsJob;
use App\Models\FlickrContact;
use Illuminate\Support\Facades\Event;

class FlickrContactObserve
{
    public function created(FlickrContact $contact)
    {
        Event::dispatch(new ContactCreated($contact));
    }

    public function updated(FlickrContact $contact)
    {
        if (!$contact->isDirty('state_code')) {
            return;
        }

        if ($contact->state_code !== FlickrContact::STATE_PHOTOS_COMPLETED) {
            return;
        }

        ContactAlbumbsJob::dispatch($contact);
    }
}
