<?php

namespace App\Jobs\Flickr;

use App\Models\FlickrPhoto;
use App\Services\FlickrService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PhotoSizeJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private FlickrPhoto $photo;

    public function __construct(FlickrPhoto $photo)
    {
        $this->photo = $photo;
    }

    public function handle()
    {
        $service = app(FlickrService::class);
        if (!$sizes = $service->getPhotoSize($this->photo->id)) {
            return;
        }

        $this->photo->update([
            'sizes' => $sizes
        ]);
    }
}
