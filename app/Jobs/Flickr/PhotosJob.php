<?php


namespace App\Jobs\Flickr;


use App\Models\FlickrContact;
use App\Models\FlickrPhoto;
use App\Services\FlickrService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PhotosJob implements ShouldQueue, ShouldBeUnique
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
        $photos = $service->getAllPhotos($this->contact->nsid);
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
