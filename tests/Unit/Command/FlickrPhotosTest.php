<?php

namespace Tests\Unit\Command;

use App\Models\FlickrContact;
use App\Models\Integration;
use App\Services\FlickrService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

class FlickrPhotosTest extends TestCase
{
    use RefreshDatabase;

    private FlickrService|MockObject $mocker;

    public function setUp(): void
    {
        parent::setUp();

        Integration::factory()->create([
            'service' => 'flickr',
            'token' => $this->faker->uuid,
            'token_secret' => $this->faker->uuid
        ]);

        $this->mocker = $this->getMockBuilder(FlickrService::class)->getMock();
        $this->fixtures = __DIR__ . '/../../Fixtures/Flickr';
        $this->mocker->method('getAllPhotos')
            ->willReturn(collect(json_decode($this->getFixture('photos.json'), true)));
        app()->instance(FlickrService::class, $this->mocker);
    }

    public function test_get_photos_of_command()
    {
        FlickrContact::factory()->create([
            'nsid' => '100028207@N03',
            'state_code' => FlickrContact::STATE_PEOPLE_INFO
        ]);

        $this->artisan('flickr:photos');
        $this->assertDatabaseHas('flickr_contacts', [
            'nsid' => '100028207@N03',
            'state_code' => FlickrContact::STATE_PHOTOS_COMPLETED
        ]);

        $this->assertDatabaseHas('flickr_photos', [
            'owner' => '100028207@N03',
            'id' => '9704307657'
        ]);
    }
}
