<?php

namespace App\Flickr\Tests\Feature\Jobs;

use App\Flickr\Tests\AbstractFlickrTest;
use App\Jobs\Flickr\GetFavoritePhotosJob;
use App\Models\FlickrContact;

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
        $this->mockSucceed();

        GetFavoritePhotosJob::dispatch($this->contact);
        $this->assertDatabaseCount('flickr_photos', 100);

        $favoritePhotos = json_decode($this->getFixture('contact_favorite_photos.json'), true);
        $photos = collect($favoritePhotos[0]['photos']['photo']);

        $this->assertDatabaseCount('flickr_contacts', $photos->groupBy('owner')->count() + 1);
        $this->assertDatabaseCount('flickr_photos', $photos->count());
    }

    public function test_cant_get_contact_favorites_photos()
    {
        $this->mockFailed();

        GetFavoritePhotosJob::dispatch($this->contact);
        $this->assertDatabaseCount('flickr_photos', 0);
        $this->assertEquals(FlickrContact::STATE_INFO_FAILED, $this->contact->refresh()->state_code);
    }
}
