<?php

namespace App\Console\Commands\Flickr;

use App\Jobs\Flickr\DownloadJob;
use App\Models\FlickrAlbum;
use App\Models\FlickrContact;
use App\Models\FlickrDownload;
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
    protected $description = 'Download Flickr photo';

    public function handle(FlickrService $service)
    {
        $type = $this->argument('type');
        $url = $this->argument('url');
        $toWordPress = $this->option('toWordPress');

        if (!$userInfo = $service->client->urls()->lookupUser($url)) {
            return;
        }

        $nsid = $userInfo['id'];
        $this->output->title('Process nsid ' . $nsid);
        $url = explode('/', $url);

        /**
         * Create contact if not exist yet
         * Use STATE_MANUAL to prevent observe jobs
         */
        $contact = FlickrContact::firstOrCreate([
            'nsid' => $nsid,
        ], ['state_code' => FlickrContact::STATE_MANUAL]);

        switch ($type) {
            case 'album':
                $albumId = end($url);
                if (!$albumInfo = $service->getAlbumInfo($albumId, $nsid)) {
                    $this->output->error('Can not get album info');
                    return;
                }

                /**
                 * Create album with STATE_MANUALLY to prevent observe job
                 */
                $album = FlickrAlbum::updateOrCreate([
                    'id' => $albumInfo['id'],
                    'owner' => $nsid,
                ], [
                    'primary' => $albumInfo['primary'],
                    'secret' => $albumInfo['secret'],
                    'server' => $albumInfo['server'],
                    'farm' => $albumInfo['farm'],
                    'photos' => $albumInfo['count_photos'],
                    'title' => $albumInfo['title'],
                    'description' => $albumInfo['description'],
                    'state_code' => FlickrAlbum::STATE_MANUAL
                ]);
                $this->output->info('Album ' . $album->id);

                $flickrDownload = FlickrDownload::create([
                    'name' => $album->title,
                    'path' => $album->owner . '/' . Str::slug($album->title),
                    'total' => $album->photos,
                    'model_id' => $album->id,
                    'model_type' => FlickrAlbum::class,
                    'state_code' => $toWordPress ? FlickrDownload::STATE_TO_WORDPRESS : FlickrDownload::STATE_INIT,
                ]);

                DownloadJob::dispatch($flickrDownload);
                break;
            case 'profile':
                $this->output->info('Profile ' . $contact->id);

                if (!$userInfoDetail = $service->getPeopleInfo($contact->nsid)->first()) {
                    return;
                }

                // Create download request
                $flickrDownload = FlickrDownload::create([
                    'name' => $contact->nsid,
                    'path' => $contact->nsid,
                    'total' => $userInfoDetail['photos']['count'],
                    'model_id' => $contact->nsid,
                    'model_type' => FlickrContact::class,
                    'state_code' => $toWordPress ? FlickrDownload::STATE_TO_WORDPRESS : FlickrDownload::STATE_INIT,
                ]);

                DownloadJob::dispatch($flickrDownload);
                break;
        }
    }
}
