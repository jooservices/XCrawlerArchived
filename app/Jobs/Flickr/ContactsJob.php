<?php

namespace App\Jobs\Flickr;

use App\Models\FlickrContact;
use App\Services\Flickr\FlickrService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

/**
 * Get all contacts of authorized user
 * @package App\Jobs\Flickr
 */
class ContactsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;

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
