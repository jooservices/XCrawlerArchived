<?php

namespace App\Console\Commands\Flickr;

use App\Models\FlickrContact;
use App\Models\FlickrDownload;
use App\Models\FlickrDownloadItem;
use App\Models\FlickrPhoto;
use App\Services\Flickr\FlickrService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class Download extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'flickr:download {type} {url} {--toWordPress}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download specific album';

    public function handle(FlickrService $service)
    {
        $type = $this->argument('type');
        $url = $this->argument('url');
        $toWordPress = $this->option('toWordPress');

        if (!$userInfo = $service->client->urls()->lookupUser($url)) {
            return;
        }

        $nsid = $userInfo['id'];
        $url = explode('/', $url);

        switch ($type) {
            case 'album':
                $albumId = end($url);
                if (!$albumInfo = $service->getAlbumInfo($albumId, $nsid)) {
                    $this->output->error('Can not get album info');
                    return;
                }
                // Create download request
                $flickrDownload = FlickrDownload::create([
                    'name' => $albumInfo['title'],
                    'path' => $albumInfo['owner'] . '/' . Str::slug($albumInfo['title']),
                    'total' => $albumInfo['count_photos'],
                    'state_code' => $toWordPress ? FlickrDownload::STATE_TO_WORDPRESS : FlickrDownload::STATE_INIT,
                ]);

                // Get photos
                if (!$photos = $service->getAlbumPhotos($albumId)) {
                    return;
                }

                /**
                 * Create contact if not exist yet
                 * Use STATE_MANUAL to prevent observe jobs
                 */
                FlickrContact::firstOrCreate([
                    'nsid' => $nsid,
                ], ['state_code' => FlickrContact::STATE_MANUAL]);

                // Process download
                $photos->each(function ($photos) use ($flickrDownload) {
                    foreach ($photos['photo'] as $photo) {
                        // Create photos
                        $photo = FlickrPhoto::updateOrCreate([
                            'id' => $photo['id'],
                            'owner' => $photos['owner'],
                        ], [
                            'secret' => $photo['secret'],
                            'server' => $photo['server'],
                            'farm' => $photo['farm'],
                            'title' => $photo['title'],
                            'state_code' => FlickrPhoto::STATE_INIT
                        ]);

                        // Create download item
                        FlickrDownloadItem::create([
                            'download_id' => $flickrDownload->id,
                            'photo_id' => $photo->id,
                            'state_code' => $this->option('toWordPress') ? FlickrDownloadItem::STATE_WORDPRESS_INIT : FlickrDownloadItem::STATE_INIT
                        ]);
                    }
                });
                break;
            case 'profile':
                $userInfoDetail = $service->getPeopleInfo($nsid)->first();
                if (!$userInfoDetail) {
                    return;
                }

                /**
                 * Create contact if not exist yet
                 * Use STATE_MANUAL to prevent observe jobs
                 */
                FlickrContact::firstOrCreate([
                    'nsid' => $nsid,
                ], ['state_code' => FlickrContact::STATE_MANUAL]);

                // Create download request
                $flickrDownload = FlickrDownload::create([
                    'name' => $nsid,
                    'path' => Str::slug($nsid),
                    'total' => $userInfoDetail['photos']['count'],
                    'state_code' => $toWordPress ? FlickrDownload::STATE_TO_WORDPRESS : FlickrDownload::STATE_INIT,
                ]);

                $photos = $service->getAllPhotos($userInfoDetail['nsid']);
                // Process download
                $photos->each(function ($page) use ($flickrDownload, $nsid) {
                    foreach ($page['photo'] as $photo) {
                        // Create photos
                        $photo = FlickrPhoto::updateOrCreate([
                            'id' => $photo['id'],
                            'owner' => $nsid,
                        ], [
                            'secret' => $photo['secret'],
                            'server' => $photo['server'],
                            'farm' => $photo['farm'],
                            'title' => $photo['title'],
                            'state_code' => FlickrPhoto::STATE_INIT
                        ]);

                        // Create download item
                        FlickrDownloadItem::create([
                            'download_id' => $flickrDownload->id,
                            'photo_id' => $photo->id,
                            'state_code' => $this->option('toWordPress') ? FlickrDownloadItem::STATE_WORDPRESS_INIT : FlickrDownloadItem::STATE_INIT
                        ]);
                    }
                });
                break;
        }
    }
}
