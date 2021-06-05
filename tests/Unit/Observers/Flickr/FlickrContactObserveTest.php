<?php

namespace Tests\Unit\Observers\Flickr;

use App\Jobs\Flickr\ContactAlbumbsJob;
use App\Jobs\Flickr\ContactInfoJob;
use App\Models\FlickrContact;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class FlickrContactObserveTest extends TestCase
{
    public function test_contact_created()
    {
        Queue::fake();
        $contact = FlickrContact::factory()->create([
            'state_code' => FlickrContact::STATE_INIT
        ]);

        Queue::assertPushed(ContactInfoJob::class, function ($event) use ($contact) {
            return $event->contact->nsid = $contact->nsid;
        });
    }

    public function test_update_flickr_contact_photos_completed_will_trigger_contact_albums()
    {
        Queue::fake();
        $contact = FlickrContact::factory()->create([
            'state_code' => FlickrContact::STATE_INIT
        ]);

        Queue::assertNotPushed(ContactAlbumbsJob::class);

        $contact->updateState(FlickrContact::STATE_PHOTOS_COMPLETED);
        Queue::assertPushed(ContactAlbumbsJob::class, function ($event) use ($contact) {
            return $event->contact->nsid = $contact->nsid;
        });
    }
}
