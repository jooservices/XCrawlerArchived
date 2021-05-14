<?php

namespace App\Jobs\Flickr;

use App\Models\FlickrAlbum;
use App\Models\FlickrPhoto;
use App\Services\FlickrService;
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

    /**
     * The number of seconds after which the job's unique lock will be released.
     *
     * @var int
     */
    public int $uniqueFor = 900;

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
        return $this->album->id;
    }

    public function handle()
    {
        $this->album->updateState(FlickrAlbum::STATE_PHOTOS_PROCESSING);
        $photos = app(FlickrService::class)->getAlbumPhotos($this->album->id);
        $photos->each(function ($photos) {
            foreach ($photos['photo'] as $photo) {
                $photo = FlickrPhoto::firstOrCreate([
                    'id' => $photo['id'],
                    'owner' => $photos['owner']
                ]);

                $this->album->photos()->syncWithoutDetaching([$photo->id]);
            }
        });

        $this->album->updateState(FlickrAlbum::STATE_PHOTOS_COMPLETED);
    }
}
