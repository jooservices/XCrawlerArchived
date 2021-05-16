<?php

namespace App\Jobs\Flickr;

use App\Jobs\Traits\HasUnique;
use App\Models\FlickrAlbum;
use App\Models\FlickrPhoto;
use App\Services\Flickr\FlickrService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Get photos of album
 * @package App\Jobs\Flickr
 */
class AlbumPhotosJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use HasUnique;

    private FlickrAlbum $album;

    public function __construct(FlickrAlbum $album)
    {
        $this->album = $album;
    }

    /**
     * The unique ID of the job.
     *
     * @return string
     */
    public function uniqueId(): string
    {
        return $this->getUnique([$this->album->id]);
    }

    public function handle()
    {
        $this->album->updateState(FlickrAlbum::STATE_PHOTOS_PROCESSING);
        $photos = app(FlickrService::class)->getAlbumPhotos($this->album->id);

        if ($photos->isEmpty()) {
            $this->album->updateState(FlickrAlbum::STATE_PHOTOS_FAILED);
            return;
        }

        $photos->each(function ($photos) {
            foreach ($photos['photo'] as $photo) {
                $photo = FlickrPhoto::firstOrCreate([
                    'id' => $photo['id'],
                    'owner' => $photos['owner']
                ], ['state_code' => FlickrPhoto::STATE_INIT]);

                $this->album->photos()->syncWithoutDetaching([$photo->id]);
            }
        });

        $this->album->updateState(FlickrAlbum::STATE_PHOTOS_COMPLETED);
    }
}
