<?php

namespace App\Jobs\Flickr;

use App\Models\FlickrAlbum;
use App\Models\FlickrContact;
use App\Services\FlickrService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Get albums of contact
 * @package App\Jobs\Flickr
 */
class ContactAlbumbsJob implements ShouldQueue, ShouldBeUnique
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

    private FlickrContact $contact;

    public function __construct(FlickrContact $contact)
    {
        $this->contact = $contact;
    }

    /**
     * Determine the time at which the job should timeout.
     *
     * @return \DateTime
     */
    public function retryUntil()
    {
        return now()->addHours(6);
    }

    /**
     * The unique ID of the job.
     *
     * @return string
     */
    public function uniqueId(): string
    {
        return $this->contact->nsid;
    }

    public function handle()
    {
        $this->contact->updateState(FlickrContact::STATE_ALBUM_PROCESSING);
        $service = app(FlickrService::class);
        $albums = $service->getContactAlbums($this->contact->nsid);
        $albums->each(function ($albums) {
            foreach ($albums['photoset'] as $album) {
                FlickrAlbum::updateOrCreate([
                    'id' => $album['id'],
                    'owner' => $album['owner'],
                ], array_merge($album, ['state_code' => FlickrAlbum::STATE_INIT]));
            }
        });

        $this->contact->updateState(FlickrContact::STATE_ALBUM_COMPLETED);
    }
}
