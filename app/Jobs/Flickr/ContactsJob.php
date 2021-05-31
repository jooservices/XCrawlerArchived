<?php

namespace App\Jobs\Flickr;

use App\Models\FlickrContact;
use App\Services\Flickr\FlickrService;

/**
 * Get all contacts of authorized user
 * @package App\Jobs\Flickr
 */
class ContactsJob extends AbstractFlickrJob
{
    public function handle(FlickrService $service)
    {
        $service->getAllContacts()->each(function ($page) {
            foreach ($page['contact'] as $contact) {
                FlickrContact::updateOrCreate(
                    ['nsid' => $contact['nsid']],
                    array_merge($contact, ['state_code' => FlickrContact::STATE_INIT])
                );
            }
        });
    }
}
