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
        $pages = $contacts->first()['pages'];

        if (1 === $pages) {
            return $contacts;
        }

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

    public function getAllPhotos(string $nsid, array $options = []): Collection
    {
        $photos = $this->client->people()->getPhotos(
            $nsid,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            500
        );

        $photos = collect()->add($photos);
        $pages = $photos->first()['pages'];

        if (1 === $pages) {
            return $photos;
        }

        for ($page = 2; $page <= $pages; ++$page) {
            $pagePhotos = $this->client->people()->getPhotos(
                $nsid,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                500,
                $page
            );
            $photos->add($pagePhotos);
        }

        return $photos;
    }

    public function getPhotoSize(string $photoId)
    {
        return $this->client->photos()->getSizes($photoId);
    }

    public function getAlbumInfo(string $albumId, string $nsid)
    {
        return $this->client->photosets()->getInfo($albumId, $nsid);
    }

    public function getAlbumPhotos(string $albumId): Collection
    {
        if (empty($albumId)) {
            return collect();
        }

        $photos = $this->client->photosets()->getPhotos(
            $albumId,
            null,
            null,
            500
        );

        $photos = collect()->add($photos);
        $pages = $photos->first()['pages'];

        if (1 === $pages) {
            return $photos;
        }

        for ($page = 2; $page <= $pages; ++$page) {
            $pagePhotos = $this->client->photosets()->getPhotos(
                $albumId,
                null,
                null,
                500,
                $page,
                $photos->add($pagePhotos)
            );

        }

        return $photos;
    }

    public function getContactAlbums(string $nsid): Collection
    {
        $albums = $this->client->photosets()->getList($nsid, null, 500);
        $albums = collect()->add($albums);
        $pages = $albums->first()['pages'];

        if (1 === $pages) {
            return $albums;
        }

        for ($page = 2; $page <= $pages; ++$page) {
            $pageAlbums = $this->client->photosets()->getList(
                $nsid,
                $page,
                500
            );
            $albums->add($pageAlbums);
        }

        return $albums;
    }
}
