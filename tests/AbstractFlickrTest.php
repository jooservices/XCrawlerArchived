<?php

namespace Tests;

use App\Models\FlickrContact;
use App\Models\Integration;
use App\Services\Flickr\FlickrService;
use PHPUnit\Framework\MockObject\MockObject;

abstract class AbstractFlickrTest extends TestCase
{
    protected FlickrService|MockObject $mocker;

    public function setUp(): void
    {
        parent::setUp();

        Integration::factory()->create([
            'service' => 'flickr',
            'token' => $this->faker->uuid,
            'token_secret' => $this->faker->uuid
        ]);
    }

    protected function mockSucceed()
    {
        $this->mocker = $this->getMockBuilder(FlickrService::class)->getMock();
        $this->fixtures = __DIR__ . '/Fixtures/Flickr';
        $mocks = [
            'getAllContacts' => 'contacts.json',
            'getPeopleInfo' => 'contact.json',
            'getAllPhotos' => 'photos.json',
            'getPhotoSize' => 'sizes.json',
            'getContactAlbums' => 'albums.json',
        ];

        foreach ($mocks as $method => $file) {
            $this->mocker->method($method)
                ->willReturn(collect(json_decode($this->getFixture($file), true)));
        }

        app()->instance(FlickrService::class, $this->mocker);
    }

    protected function mockFailed()
    {
        $this->mocker = $this->getMockBuilder(FlickrService::class)->getMock();
        $this->fixtures = __DIR__ . '/Fixtures/Flickr';
        $mocks = [
            'getAllContacts' => collect(),
            'getPeopleInfo' => null,
            'getAllPhotos' => collect(),
            'getPhotoSize' => null,
        ];

        foreach ($mocks as $method => $response) {
            $this->mocker->method($method)
                ->willReturn($response);
        }

        app()->instance(FlickrService::class, $this->mocker);
    }

    protected function factoryContact(): FlickrContact
    {
        return FlickrContact::factory()->create(
            [
                'nsid' => '100028207@N03',
                'state_code' => FlickrContact::STATE_INIT
            ]
        );
    }
}
