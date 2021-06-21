<?php

namespace App\Flickr\Tests\Unit\Services;

use App\Flickr\Tests\AbstractFlickrTest;
use App\Services\Flickr\FlickrService;
use Illuminate\Support\Collection;

class FlickrServiceTest extends AbstractFlickrTest
{
    protected FlickrService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->buildMock(true);
        $this->service = app(FlickrService::class);
    }

    public function test_get_all_contacts()
    {
        $contacts = $this->service->getAllContacts();

        $this->assertInstanceOf(Collection::class, $contacts);
        $this->assertEquals(1000, count($contacts->first()['contact']));
        $this->assertEquals(84, count($contacts->last()['contact']));
        $this->assertEquals(2, $contacts->count());
        $this->assertEquals(1000, $contacts->first()['per_page']);
        $this->assertEquals(1, $contacts->first()['page']);
        $this->assertEquals(2, $contacts->last()['page']);
    }

    public function test_get_people_info()
    {
        $people = $this->service->getPeopleInfo($this->faker->uuid);

        $this->assertEquals('94529704@N02', $people->get('person')['id']);
        $this->assertEquals('SoulEvilX', $people->get('person')['username']);
    }

    public function test_get_all_photos()
    {
        $photos = $this->service->getAllPhotos($this->faker->uuid);

        $this->assertEquals(500, $photos->first()['perpage']);
        $this->assertEquals(1, $photos->first()['page']);
        $this->assertEquals(358, $photos->first()['total']);
        $this->assertEquals($photos->first()['total'], count($photos->first()['photo']));
    }

    public function test_get_photo_size()
    {
        $sizes = $this->service->getPhotoSize($this->faker->uuid);
        $this->assertEquals(11, count($sizes['size']));

        $size = end($sizes['size']);
        $this->assertEquals(5057, $size['width']);
        $this->assertEquals(3636, $size['height']);
        $this->assertEquals('photo', $size['media']);
    }

    public function test_get_album_info()
    {
        $album = $this->service->getAlbumInfo($this->faker->uuid, $this->faker->uuid);

        $this->assertEquals(72157692139427840, $album->get('id'));
        $this->assertEquals('94529704@N02', $album->get('owner'));
        $this->assertEquals('SoulEvilX', $album->get('username'));
        $this->assertEquals('Vy Trần - Are you 18+', $album->get('title'));
    }

    public function test_get_album_photos()
    {
        $photos = $this->service->getAlbumPhotos($this->faker->uuid);

        $this->assertEquals(1, $photos->count());
        $this->assertEquals('94529704@N02', $photos->first()['owner']);
        $this->assertEquals(16, count($photos->first()['photo']));
    }

    public function test_get_contact_albums()
    {
        $albums = $this->service->getContactAlbums($this->faker->uuid);

        $this->assertEquals(1, $albums->count());
        $this->assertEquals(23, count($albums->first()['photoset']));
        $this->assertEquals(23, $albums->first()['total']);
    }

    public function test_get_favorites_list()
    {
        $favorites = $this->service->getFavoritePhotos($this->faker->uuid);

        $this->assertEquals(18, $favorites->count());
        $this->assertEquals(100, $favorites->first()['photos']['perpage']);
        $this->assertEquals(1702, $favorites->first()['photos']['total']);
        $this->assertEquals(100, count($favorites->first()['photos']['photo']));
    }
}
