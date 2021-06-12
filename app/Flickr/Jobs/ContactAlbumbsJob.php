<?php

namespace App\Flickr\Jobs;

use App\Models\FlickrAlbum;
use App\Models\FlickrContact;
use App\Services\Flickr\FlickrService;

/**
 * Get albums of contact
 * @package App\Flickr\Jobs\
 */
class ContactAlbumbsJob extends AbstractFlickrJob
{
    public FlickrContact $contact;

    public function __construct(FlickrContact $contact)
    {
        $this->contact = $contact;
    }

    /**
     * The unique ID of the job.
     *
     * @return string
     */
    public function uniqueId(): string
    {
        return $this->getUnique([$this->contact->nsid]);
    }

    public function handle(FlickrService $service)
    {
        $this->contact->updateState(FlickrContact::STATE_ALBUM_PROCESSING);
        $albums = $service->getContactAlbums($this->contact->nsid);
        if ($albums->isEmpty()) {
            $this->contact->updateState(FlickrContact::STATE_ALBUM_FAILED);
            return;
        }

        $albums->each(function ($albums) {
            foreach ($albums['photoset'] as $album) {
                $title = $album['title'] ?? null;
                $album['title'] = is_array($title) ? $title['_content'] : $title;

                $description = $album['description'] ?? null;
                $album['description'] = is_array($description) ? $description['_content'] : $description;

                FlickrAlbum::updateOrCreate([
                    'id' => $album['id'],
                    'owner' => $album['owner'],
                ], array_merge($album, ['state_code' => FlickrAlbum::STATE_INIT]));
            }
        });

        $this->contact->updateState(FlickrContact::STATE_ALBUM_COMPLETED);
    }
}
