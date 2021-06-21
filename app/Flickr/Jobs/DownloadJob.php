<?php

namespace App\Flickr\Jobs;

use App\Models\FlickrAlbum;
use App\Models\FlickrContact;
use App\Models\FlickrDownload;
use App\Models\FlickrDownloadItem;
use App\Models\FlickrPhoto;
use App\Services\Flickr\FlickrService;
use Illuminate\Support\Str;

class DownloadJob extends AbstractFlickrJob
{
    public function __construct(public string $url, public string $type, public bool $toWordPress)
    {
    }

    /**
     * The unique ID of the job.
     *
     * @return string
     */
    public function uniqueId(): string
    {
        return $this->getUnique([$this->url]);
    }

    public function handle(FlickrService $service)
    {
        if (!$userInfo = $service->client->urls()->lookupUser($this->url)) {
            return;
        }

        $nsid = $userInfo['id'];
        $url = explode('/', $this->url);

        /**
         * Create contact if not exist yet
         * Use STATE_MANUAL to prevent observe jobs
         */
        $contact = FlickrContact::firstOrCreate([
            'nsid' => $nsid,
        ], ['state_code' => FlickrContact::STATE_MANUAL]);

        switch ($this->type) {
            case 'album':
                $albumId = end($url);
                if (!$albumInfo = $service->getAlbumInfo($albumId, $nsid)) {
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

                $flickrDownload = FlickrDownload::create([
                    'name' => $album->title,
                    'path' => $album->owner . '/' . Str::slug($album->title),
                    'total' => $album->photos,
                    'model_id' => $album->id,
                    'model_type' => FlickrAlbum::class,
                    'state_code' => $this->toWordPress ? FlickrDownload::STATE_TO_WORDPRESS : FlickrDownload::STATE_INIT,
                ]);

                $this->downloadAlbum($album, $flickrDownload, $service);
                break;
            case 'profile':
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
                    'state_code' => $this->toWordPress ? FlickrDownload::STATE_TO_WORDPRESS : FlickrDownload::STATE_INIT,
                ]);

                $this->downloadProfile($contact, $flickrDownload, $service);
                break;
        }
    }

    private function downloadAlbum(FlickrAlbum $album, FlickrDownload $download, FlickrService $service)
    {
        $photos = $service->getAlbumPhotos($album->id);
        $photos->each(function ($photos) use ($download) {
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
                    'download_id' => $download->id,
                    'photo_id' => $photo->id,
                    'state_code' => $download->state_code === FlickrDownload::STATE_TO_WORDPRESS ? FlickrDownloadItem::STATE_WORDPRESS_INIT : FlickrDownloadItem::STATE_INIT
                ]);
            }
        });
    }

    private function downloadProfile(FlickrContact $contact, FlickrDownload $download, FlickrService $service)
    {
        $photos = $service->getAllPhotos($contact->nsid);
        $photos->each(function ($page) use ($download) {
            foreach ($page['photo'] as $photo) {
                // Create photos
                $photo = FlickrPhoto::updateOrCreate([
                    'id' => $photo['id'],
                    'owner' => $photo['owner'],
                ], [
                    'secret' => $photo['secret'],
                    'server' => $photo['server'],
                    'farm' => $photo['farm'],
                    'title' => $photo['title'],
                    'state_code' => FlickrPhoto::STATE_INIT
                ]);

                // Create download item
                FlickrDownloadItem::create([
                    'download_id' => $download->id,
                    'photo_id' => $photo->id,
                    'state_code' => $download->state_code === FlickrDownload::STATE_TO_WORDPRESS ? FlickrDownloadItem::STATE_WORDPRESS_INIT : FlickrDownloadItem::STATE_INIT
                ]);
            }
        });
    }
}
