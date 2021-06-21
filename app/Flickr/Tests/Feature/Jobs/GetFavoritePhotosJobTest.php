<?php

namespace App\Flickr\Tests\Feature\Jobs;

use App\Flickr\Jobs\GetFavoritePhotosJob;
use App\Flickr\Tests\AbstractFlickrTest;
use App\Models\FlickrContact;
use App\Services\Flickr\FlickrService;

class GetFavoritePhotosJobTest extends AbstractFlickrTest
{
    private FlickrContact $contact;

    public function setUp(): void
    {
        parent::setUp();
        $this->contact = FlickrContact::factory()->create(['state_code' => FlickrContact::STATE_MANUAL]);
    }

    public function test_get_contact_favorites_photos()
    {
        $this->buildMock(true);
        $this->service = app(FlickrService::class);

        GetFavoritePhotosJob::dispatch($this->contact);
        $this->assertDatabaseCount('flickr_photos', 100);

        $favoritePhotos = json_decode($this->getFixture('favorites_list.json'), true);
        $photos = collect($favoritePhotos['photos']['photo']);

        $this->assertDatabaseCount('flickr_contacts', $photos->groupBy('owner')->count() + 1);
        $this->assertDatabaseCount('flickr_photos', $photos->count());
    }

    public function test_cant_get_contact_favorites_photos()
    {
        $this->buildMock(false);
        $this->service = app(FlickrService::class);

        GetFavoritePhotosJob::dispatch($this->contact);
        $this->assertDatabaseCount('flickr_photos', 0);
        $this->assertEquals(FlickrContact::STATE_INFO_FAILED, $this->contact->refresh()->state_code);
    }
}
