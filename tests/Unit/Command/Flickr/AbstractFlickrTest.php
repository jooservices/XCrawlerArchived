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
        $this->mocker->method('getAllPhotos')
            ->willReturn(collect(json_decode($this->getFixture('photos.json'), true)));
        app()->instance(FlickrService::class, $this->mocker);
    }
}
