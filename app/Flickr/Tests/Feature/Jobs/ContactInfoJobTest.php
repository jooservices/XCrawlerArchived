<?php

namespace App\Flickr\Tests\Feature\Jobs;

use App\Flickr\Jobs\ContactInfoJob;
use App\Flickr\Tests\AbstractFlickrTest;
use App\Models\FlickrContact;
use App\Services\Flickr\FlickrService;
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
        $this->buildMock(true);
        $this->service = app(FlickrService::class);
        $contact = $this->factoryContact();

        ContactInfoJob::dispatch($contact);
        $contact->refresh();
        $this->assertEquals(FlickrContact::STATE_INFO_COMPLETED, $contact->state_code);
        $this->assertEquals('soulevilx', $contact->path_alias);
        $this->assertEquals('SoulEvilX', $contact->username);
        // @TODO Assert all fields
    }

    public function test_cant_get_info()
    {
        $this->buildMock(false);
        $this->service = app(FlickrService::class);
        $contact = $this->factoryContact();

        ContactInfoJob::dispatch($contact);
        $this->assertEquals(FlickrContact::STATE_INFO_FAILED, $contact->refresh()->state_code);
    }
}
