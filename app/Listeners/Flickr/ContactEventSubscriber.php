<?php

namespace App\Listeners\Flickr;

use App\Events\Flickr\ContactCreated;
use App\Events\Flickr\ContactStateChanged;
use App\Jobs\Flickr\ContactAlbumbsJob;
use App\Jobs\Flickr\ContactInfoJob;
use App\Jobs\Flickr\PhotosJob;
use App\Models\FlickrContact;

class ContactEventSubscriber
{
    public function getContactInfo($event)
    {
        ContactInfoJob::dispatch($event->contact);
    }

    public function processContact(ContactStateChanged $event)
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
        ], self::class . '@getContactInfo');

        $events->listen([
            ContactStateChanged::class,
        ], self::class . '@processContact');
    }
}
