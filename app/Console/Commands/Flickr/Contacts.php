<?php

namespace App\Console\Commands\Flickr;

use App\Models\FlickrContact;
use App\Services\FlickrService;
use Illuminate\Console\Command;

class Contacts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'flickr:contacts {--url=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch Flickr ALL contacts of authorized user';

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
                    [
                        'nsid' => $contact['nsid']
                    ],
                    array_merge($contact, ['state_code' => FlickrContact::STATE_INIT])
                );
            }
        });
    }
}
