<?php

namespace App\Flickr\Listeners;

use App\Flickr\Events\ContactCreated;
use App\Flickr\Events\ContactStateChanged;
use App\Flickr\Jobs\ContactAlbumbsJob;
use App\Flickr\Jobs\ContactInfoJob;
use App\Flickr\Jobs\PhotosJob;
use App\Models\FlickrContact;

class ContactEventSubscriber
{
    public function processContact($event)
    {
        /**
         * Contact create will dispatch info job than update STATE_INFO_COMPLETED
         * STATE_INFO_COMPLETED will dispatch get photos than update STATE_PHOTOS_COMPLETED
         * STATE_PHOTOS_COMPLETED will dispatch get albums than update STATE_ALBUM_COMPLETED
         */
        $contact = $event->contact;
        switch ($contact->state_code) {
            case FlickrContact::STATE_INIT:
                ContactInfoJob::dispatch($event->contact); // STATE_INFO_COMPLETED
                break;
            case FlickrContact::STATE_INFO_COMPLETED:
                PhotosJob::dispatch($contact); // STATE_PHOTOS_COMPLETED
                // @TODO Get favorite photos
                break;
            case FlickrContact::STATE_PHOTOS_COMPLETED:
                ContactAlbumbsJob::dispatch($contact);
                break;
        }
    }

    public function subscribe($events)
    {
        $events->listen([
            ContactCreated::class,
            ContactStateChanged::class,
        ], self::class . '@processContact');
    }
}
