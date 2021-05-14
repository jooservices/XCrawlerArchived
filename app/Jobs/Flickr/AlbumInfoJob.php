<?php

namespace App\Jobs\Flickr;

use App\Models\FlickrAlbum;
use App\Services\FlickrService;
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

    private string $albumId;
    private string $nsid;

    public function __construct(string $albumId, string $nsid)
    {
        $this->albumId = $albumId;
        $this->nsid = $nsid;
    }

    public function handle()
    {
        $album = app(FlickrService::class)->getAlbumInfo($this->albumId, $this->nsid);

        FlickrAlbum::updateOrCreate([
            'id' => $album['id'],
            'owner' => $album['owner']
        ], array_merge($album, ['state_code' => FlickrAlbum::STATE_INIT]));
    }
}
