<?php

namespace App\Services\Flickr;

use App\Models\Integration;
use App\Services\Client\Domain\ResponseInterface;
use App\Services\Client\FlickrClientResponse;
use Jooservices\PhpFlickr\FlickrException;
use OAuth\Common\Storage\Memory;
use OAuth\OAuth1\Token\StdOAuth1Token;

class AbstractFlickrService
{
    protected ?Integration $integration;
    public PhpFlickr $client;

    /**
     * @throws FlickrException
     */
    public function __construct()
    {
        $this->integration = Integration::forService('flickr')->first();
        if (!$this->integration) {
            throw new FlickrException('There is no integration yet');
        }

        $storage = new Memory();
        $token = new StdOAuth1Token();
        $token->setAccessToken($this->integration->token);
        $token->setAccessTokenSecret($this->integration->token_secret);
        $storage->storeAccessToken('Flickr', $token);

        $this->client = new PhpFlickr(config('services.flickr.client_id'), config('services.flickr.client_secret'));
        $this->client->setOauthStorage($storage);

        app()->bind(ResponseInterface::class, FlickrClientResponse::class);
    }
}
