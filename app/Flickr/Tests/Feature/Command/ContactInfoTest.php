<?php

namespace App\Flickr\Tests\Feature\Command;

use App\Flickr\Tests\AbstractFlickrTest;
use App\Jobs\Flickr\ContactInfoJob;
use App\Jobs\Flickr\GetFavoritePhotosJob;
use App\Models\FlickrContact;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Queue;

class ContactInfoTest extends AbstractFlickrTest
{
    public function setUp(): void
    {
        parent::setUp();
        Queue::fake();
    }

    public function test_get_contact_info()
    {
        /**
         * From now whenever contact is created it will also trigger job to get detail info
         * We do fake event here to prevent that
         * Beside that this command used for relooping when all contact info are updated
         */
        $this->mockSucceed();
        $contact = FlickrContact::factory()->create([
            'state_code' => FlickrContact::STATE_MANUAL
        ]);
        $this->artisan('flickr:contact-info');

        Queue::assertPushed(ContactInfoJob::class, function ($event) use ($contact) {
            return $event->contact->nsid = $contact->nsid;
        });

        Queue::assertPushed(GetFavoritePhotosJob::class, function ($event) use ($contact) {
            return $event->contact->nsid = $contact->nsid;
        });
    }
}
