<?php

namespace App\Console\Commands\Flickr;

use App\Models\FlickrContact;
use App\Services\FlickrService;
use Illuminate\Console\Command;

class ContactInfo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'flickr:contact-info';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch contact detail information';

    public function handle()
    {
        if (!$contact = FlickrContact::byState(FlickrContact::STATE_INIT)->first()) {
            return;
        }

        $service = app(FlickrService::class);
        $contactInfo = $service->getPeopleInfo($contact->nsid);

        if (!$contactInfo) {
            return;
        }

        $contact->update(array_merge($contactInfo['person'], ['state_code' => FlickrContact::STATE_PEOPLE_INFO]));
    }
}
