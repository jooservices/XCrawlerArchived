<?php

namespace App\Flickr\Observers;

use App\Flickr\Events\ContactCreated;
use App\Flickr\Events\ContactStateChanged;
use App\Models\FlickrContact;
use Illuminate\Support\Facades\Event;

class ContactObserver
{
    public function created(FlickrContact $contact)
    {
        Event::dispatch(new ContactCreated($contact));
    }

    public function updated(FlickrContact $contact)
    {
        if ($contact->isDirty('state_code')) {
            Event::dispatch(new ContactStateChanged($contact));
        }
    }
}
