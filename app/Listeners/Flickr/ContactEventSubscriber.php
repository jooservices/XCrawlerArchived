<?php

namespace App\Listeners\Flickr;

use App\Events\Flickr\ContactCreated;
use App\Jobs\Flickr\ContactInfoJob;

class ContactEventSubscriber
{
    public function getContactInfo($event)
    {
        ContactInfoJob::dispatch($event->contact);
    }

    public function subscribe($events)
    {
        $events->listen([
            ContactCreated::class,
        ], self::class . '@getContactInfo');
    }
}
