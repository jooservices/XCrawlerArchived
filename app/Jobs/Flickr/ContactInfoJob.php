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

class ContactInfoJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private FlickrContact $contact;

    public function __construct(FlickrContact $contact)
    {
        $this->contact = $contact;
    }

    public function handle()
    {
        $service = app(FlickrService::class);

        if (!$contactInfo  = $service->getPeopleInfo($this->contact->nsid)) {
            return;
        }

        $this->contact->update(array_merge($contactInfo['person'], ['state_code' => FlickrContact::STATE_PEOPLE_INFO]));
    }
}
