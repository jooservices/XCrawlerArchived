<?php

namespace Tests\Unit\Command\Flickr;

use App\Models\Integration;
use App\Services\FlickrService;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

abstract class AbstractFlickrTest  extends TestCase
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

        $this->mocker = $this->getMockBuilder(FlickrService::class)->getMock();
        $this->fixtures = __DIR__ . '/../../../Fixtures/Flickr';
        $mocks = [
            'getAllContacts' => 'contacts.json',
            'getPeopleInfo' => 'contact.json',
            'getAllPhotos' => 'photos.json',
        ];

        foreach ($mocks as $method => $file)
        {
            $this->mocker->method($method)
                ->willReturn(collect(json_decode($this->getFixture($file), true)));
        }

        app()->instance(FlickrService::class, $this->mocker);
    }
}
