<?php

namespace App\Console\Commands\Flickr;

use App\Models\FlickrContact;
use App\Models\FlickrPhoto;
use App\Services\FlickrService;
use Illuminate\Console\Command;

class Photos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'flickr:photos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch Flickr photos of user';

    public function handle()
    {
        if (!$contact = FlickrContact::byState(FlickrContact::STATE_PEOPLE_INFO)->first()) {
            return;
        }

        $service = app(FlickrService::class);
        $photos = $service->getAllPhotos($contact->nsid);
        $photos->each(function ($photos) {
            foreach ($photos['photo'] as $photo) {
                FlickrPhoto::updateOrCreate([
                    'id' => $photo['id'],
                    'owner' => $photo['owner'],
                ], $photo);
            }
        });
    }
}

