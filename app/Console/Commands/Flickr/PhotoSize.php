<?php

namespace App\Console\Commands\Flickr;

use App\Models\FlickrPhoto;
use App\Services\FlickrService;
use Illuminate\Console\Command;

class PhotoSize extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'flickr:photo-size';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch Flickr photos sizes';

    public function handle()
    {
        $photos = FlickrPhoto::whereNull('sizes')->limit(50)->get();
        $service = app(FlickrService::class);
        foreach ($photos as $photo) {
            $photo->update([
                'sizes' => $service->getPhotoSize($photo->id)
            ]);
        }
    }
}

