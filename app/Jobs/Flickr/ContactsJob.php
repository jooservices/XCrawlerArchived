<?php


namespace App\Jobs\Flickr;


use App\Models\FlickrContact;
use App\Services\FlickrService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ContactsJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function handle()
    {
        $service = app(FlickrService::class);
        $contacts = $service->getAllContacts();

        if ($contacts->isEmpty()) {
            return;
        }

        $contacts->each(function ($page) {
            foreach ($page['contact'] as $contact) {
                FlickrContact::updateOrCreate(
                    ['nsid' => $contact['nsid']],
                    array_merge($contact, ['state_code' => FlickrContact::STATE_INIT])
                );
            }
        });
    }
}
