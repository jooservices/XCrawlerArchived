<?php

namespace Tests\Unit\Command\Flickr;

use App\Models\FlickrContact;

class PhotosTest extends AbstractFlickrTest
{
    public function test_get_photos_of_command()
    {
        FlickrContact::factory()->create([
            'nsid' => '100028207@N03',
            'state_code' => FlickrContact::STATE_INFO_COMPLETED
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
