<?php

namespace Tests\Unit\Jobs\Flickr;

use App\Jobs\Flickr\ContactInfoJob;
use App\Models\FlickrContact;
use Tests\AbstractFlickrTest;

class ContactInfoJobTest extends AbstractFlickrTest
{
    public function test_can_get_info()
    {
        $this->mockSucceed();
        $contact = $this->factoryContact();

        ContactInfoJob::dispatch($contact);
        $contact->refresh();
        $this->assertEquals(FlickrContact::STATE_INFO_COMPLETED, $contact->state_code);
    }

    public function test_cant_get_info()
    {
        $this->mockFailed();
        $contact = $this->factoryContact();

        ContactInfoJob::dispatch($contact);
        $contact->refresh();
        $this->assertEquals(FlickrContact::STATE_INFO_FAILED, $contact->state_code);
    }
}
