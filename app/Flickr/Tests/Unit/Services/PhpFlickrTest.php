<?php

namespace App\Flickr\Tests\Unit\Services;

use App\Events\ClientRequested;
use App\Flickr\Tests\AbstractFlickrTest;
use App\Services\Client\Domain\ResponseInterface;
use App\Services\Client\FlickrClientResponse;
use App\Services\Flickr\PhpFlickr;
use Illuminate\Support\Facades\Event;
use OAuth\Common\Storage\Memory;
use OAuth\OAuth1\Token\StdOAuth1Token;

class PhpFlickrTest extends AbstractFlickrTest
{
    private PhpFlickr $client;

    public function setUp(): void
    {
        parent::setUp();

        app()->bind(ResponseInterface::class, FlickrClientResponse::class);

        $storage = new Memory();
        $token = new StdOAuth1Token();
        $storage->storeAccessToken('Flickr', $token);

        $this->client = new PhpFlickr(config('services.flickr.client_id'), config('services.flickr.client_secret'));
        $this->client->setOauthStorage($storage);
    }

    public function test_request_failed()
    {
        Event::fake();
        $this->client->request('flickr.contacts.getList', []);

        Event::assertDispatched(ClientRequested::class);
    }
}
