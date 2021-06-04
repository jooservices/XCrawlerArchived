<?php

namespace App\Services\Flickr;

use Illuminate\Support\Collection;
use Jooservices\PhpFlickr\FlickrException;

class FlickrService extends AbstractFlickrService
{
    public function getAllContacts(): Collection
    {
        try {
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
        } catch (FlickrException $exception) {
            $this->failed(__FUNCTION__, $exception);
            return collect();
        }
    }

    public function getPeopleInfo(string $nsid): mixed
    {
        try {
            if (!$info = $this->client->people()->getInfo($nsid)) {
                return null;
            }

            return collect($info);
        } catch (FlickrException $exception) {
            $this->failed(__FUNCTION__, $exception, ['nsid' => $nsid]);
            return null;
        }
    }

    public function getAllPhotos(string $nsid): Collection
    {
        $maxPerPage = 500;
        try {
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
        } catch (FlickrException $exception) {
            $this->failed(__FUNCTION__, $exception, ['nsid' => $nsid]);
            return collect();
        }
    }

    public function getPhotoSize(string $photoId)
    {
        try {
            return $this->client->photos()->getSizes($photoId);
        } catch (FlickrException $exception) {
            $this->failed(__FUNCTION__, $exception, ['photo_id' => $photoId]);
            return null;
        }
    }

    public function getAlbumInfo(string $albumId, string $nsid)
    {
        try {
            return $this->client->photosets()->getInfo($albumId, $nsid);
        } catch (FlickrException $exception) {
            $this->failed(__FUNCTION__, $exception, ['album_id' => $albumId, 'nsid' => $nsid]);
            return null;
        }
    }

    public function getAlbumPhotos(string $albumId): Collection
    {
        if (empty($albumId)) {
            return collect();
        }

        try {
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
        } catch (FlickrException $exception) {
            $this->failed(__FUNCTION__, $exception, ['album_id' => $albumId]);
            return collect();
        }
    }

    public function getContactAlbums(string $nsid): Collection
    {
        try {
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
        } catch (FlickrException $exception) {
            $this->failed(__FUNCTION__, $exception, ['nsid' => $nsid]);
            return collect();
        }
    }
}
