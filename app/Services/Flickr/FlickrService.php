<?php

namespace App\Services\Flickr;

use Illuminate\Support\Collection;

class FlickrService extends AbstractFlickrService
{
    public function getAllContacts(): Collection
    {
        if (!$contacts = $this->client->contacts()->getList()) {
            return collect();
        }

        $pages = $contacts['contacts']['pages'];
        $contacts = collect()->add($contacts['contacts']);

        if (1 === $pages) {
            return $contacts;
        }

        for ($page = 2; $page <= $pages; ++$page) {
            $pageContacts = $this->client->contacts()->getList(null, $page);
            $contacts->add($pageContacts['contacts']);
        }

        return $contacts;
    }

    public function getPeopleInfo(string $nsid): mixed
    {
        if (!$info = $this->client->people()->getInfo($nsid)) {
            return null;
        }

        return collect($info);
    }

    public function getAllPhotos(string $nsid): Collection
    {
        $maxPerPage = 500;

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
            $maxPerPage
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
                $maxPerPage,
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

    public function getAlbumInfo(string $albumId, string $nsid): Collection
    {
        return collect($this->client->photosets()->getInfo($albumId, $nsid));
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
            );

            $photos->add($pagePhotos);
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

    public function getFavoritePhotos(string $nsid): ?Collection
    {
        $photos = $this->client->favorites()->getList(
            '123675113@N02',
            null,
            null,
            null,
            500
        );

        if (empty($photos)) {
            return null;
        }

        $pages = $photos['photos']['pages'];
        $photos = collect()->add($photos);

        if (1 === $pages) {
            return $photos;
        }

        for ($page = 2; $page <= $pages; ++$page) {
            $pagePhotos = $this->client->favorites()->getList(
                $nsid,
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
}
