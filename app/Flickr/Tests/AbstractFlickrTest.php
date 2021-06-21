<?php

namespace App\Flickr\Tests;

use Mockery\LegacyMockInterface;
use Mockery\MockInterface;
use App\Flickr\Interfaces\FlickrClientInterface;
use App\Flickr\Mock\ContactsApi;
use App\Flickr\Mock\FailedMocker;
use App\Flickr\Mock\FavoritesApi;
use App\Flickr\Mock\PeopleApi;
use App\Flickr\Mock\PhotosApi;
use App\Flickr\Mock\PhotosetsApi;
use App\Flickr\Mock\UrlsApi;
use App\Models\FlickrContact;
use App\Services\Flickr\FlickrService;
use Mockery;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

abstract class AbstractFlickrTest extends TestCase
{
    protected FlickrService|MockObject $mocker;

    protected const TOTAL_CONTACTS = 1084;
    protected const TOTAL_CONTACT_PHOTOS = 358;
    protected LegacyMockInterface|MockInterface|FlickrClientInterface $client;
    protected FlickrService $service;

    protected function buildMock(bool $isSucceed)
    {
        $this->fixtures = __DIR__ . '/Fixtures';

        $this->client = Mockery::mock(FlickrClientInterface::class);
        $apis = [
            'contacts' => new ContactsApi(),
            'people' => new PeopleApi(),
            'photos' => new PhotosApi(),
            'photosets' => new PhotosetsApi(),
            'favorites' => new FavoritesApi(),
            'urls' => new UrlsApi()
        ];
        foreach ($apis as $api => $class) {
            $this->client->shouldReceive($api)->andReturn($isSucceed ? $class : new FailedMocker());
        }

        app()->instance(FlickrClientInterface::class, $this->client);
    }


    protected function factoryContact(): FlickrContact
    {
        return FlickrContact::factory()->create(
            [
                'nsid' => '94529704@N02',
                'state_code' => FlickrContact::STATE_INIT
            ]
        );
    }
}
