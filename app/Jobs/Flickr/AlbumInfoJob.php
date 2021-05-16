<?php

namespace App\Jobs\Flickr;

use App\Jobs\Traits\HasUnique;
use App\Models\FlickrAlbum;
use App\Services\Flickr\FlickrService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Get album information
 * @package App\Jobs\Flickr
 */
class AlbumInfoJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use HasUnique;

    private string $albumId;
    private string $nsid;

    public function __construct(string $albumId, string $nsid)
    {
        $this->albumId = $albumId;
        $this->nsid = $nsid;
    }

    /**
     * The unique ID of the job.
     *
     * @return string
     */
    public function uniqueId(): string
    {
        return $this->getUnique([$this->albumId, $this->nsid]);
    }

    public function handle()
    {
        if (!$album = app(FlickrService::class)->getAlbumInfo($this->albumId, $this->nsid)) {
            FlickrAlbum::where(['id' => $this->albumId, 'owner' => $this->nsid])
                ->update(['state_code' => FlickrAlbum::STATE_INFO_FAILED]);

            return;
        }

        FlickrAlbum::updateOrCreate([
            'id' => $album['id'],
            'owner' => $album['owner']
        ], array_merge($album, ['state_code' => FlickrAlbum::STATE_INIT]));
    }
}
