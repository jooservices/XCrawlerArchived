<?php

namespace App\Flickr\Tests\Unit\Observers;

use App\Jobs\Flickr\ContactAlbumbsJob;
use App\Jobs\Flickr\ContactInfoJob;
use App\Jobs\Flickr\PhotosJob;
use App\Models\FlickrContact;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ContactObserverTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Queue::fake();
    }

    public function test_contact_created()
    {
        $contact = FlickrContact::factory()->create(['state_code' => FlickrContact::STATE_INIT]);
        Queue::assertPushed(ContactInfoJob::class, function ($event) use ($contact) {
            return $event->contact->nsid = $contact->nsid;
        });
    }

    public function test_contact_info_completed()
    {
        $contact = FlickrContact::factory()->create(['state_code' => FlickrContact::STATE_INIT]);
        Queue::assertPushed(ContactInfoJob::class, function ($event) use ($contact) {
            return $event->contact->nsid = $contact->nsid;
        });

        $contact->updateState(FlickrContact::STATE_INFO_COMPLETED);
        Queue::assertPushed(PhotosJob::class, function ($event) use ($contact) {
            return $event->contact->nsid = $contact->nsid;
        });
    }

    public function test_contact_photo_completed()
    {
        $contact = FlickrContact::factory()->create(['state_code' => FlickrContact::STATE_INIT]);
        Queue::assertPushed(ContactInfoJob::class, function ($event) use ($contact) {
            return $event->contact->nsid = $contact->nsid;
        });

        $contact->updateState(FlickrContact::STATE_INFO_COMPLETED);
        Queue::assertPushed(PhotosJob::class, function ($event) use ($contact) {
            return $event->contact->nsid = $contact->nsid;
        });

        $contact->updateState(FlickrContact::STATE_PHOTOS_COMPLETED);
        Queue::assertPushed(ContactAlbumbsJob::class, function ($event) use ($contact) {
            return $event->contact->nsid = $contact->nsid;
        });
    }
}
