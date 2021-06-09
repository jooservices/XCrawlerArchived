<?php

namespace App\Flickr\Tests\Feature\Jobs;

use App\Flickr\Tests\AbstractFlickrTest;
use App\Jobs\Flickr\ContactInfoJob;
use App\Models\FlickrContact;
use Illuminate\Support\Facades\Event;

class ContactInfoJobTest extends AbstractFlickrTest
{
    public function setUp(): void
    {
        parent::setUp();
        Event::fake();
    }

    public function test_can_get_info()
    {
        $this->mockSucceed();
        $contact = $this->factoryContact();

        ContactInfoJob::dispatch($contact);
        $this->assertEquals(FlickrContact::STATE_INFO_COMPLETED, $contact->refresh()->state_code);
    }

    public function test_cant_get_info()
    {
        $this->mockFailed();
        $contact = $this->factoryContact();

        ContactInfoJob::dispatch($contact);
        $this->assertEquals(FlickrContact::STATE_INFO_FAILED,  $contact->refresh()->state_code);
    }
}
