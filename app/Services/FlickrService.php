<?php

namespace App\Services;


use App\Models\Integration;
use Illuminate\Support\Collection;
use Jooservices\PhpFlickr\PhpFlickr;
use OAuth\Common\Storage\Memory;
use OAuth\OAuth1\Token\StdOAuth1Token;


class FlickrService
{
    private Integration $integration;
    public PhpFlickr $client;

    public function __construct()
    {
        $this->integration = Integration::forService('flickr')->first();
        $storage = new Memory();
        $token = new StdOAuth1Token();
        $token->setAccessToken($this->integration->token);
        $token->setAccessTokenSecret($this->integration->token_secret);
        $storage->storeAccessToken('Flickr', $token);

        $this->client = new PhpFlickr(config('services.flickr.client_id'), config('services.flickr.client_secret'));
        $this->client->setOauthStorage($storage);
    }

    public function getAllContacts(): Collection
    {
        if (!$contacts = $this->client->contacts()->getList()) {
            return collect();
        }

        $contacts = collect()->add($contacts['contacts']);

        if (1 === $contacts->first()['pages']) {
            return $contacts;
        }

        $pages = $contacts->first()['pages'];

        for ($page = 2; $page <= $pages; ++$page) {
            $pageContacts = $this->client->contacts()->getList(null, $page);
            $contacts->add($pageContacts['contacts']);
        }

        return $contacts;
    }

    public function getPeopleInfo(string $nsid)
    {
        if (!$info = $this->client->people()->getInfo($nsid)) {
            return null;
        }

        return $info;
    }
}
