<?php

namespace Database\Factories;

use App\Models\FlickrDownload;
use App\Models\FlickrDownloadItem;
use App\Models\FlickrPhoto;
use Illuminate\Database\Eloquent\Factories\Factory;

class FlickrDownloadItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = FlickrDownloadItem::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'download_id' => FlickrDownload::factory()->create()->id,
            'photo_id' => FlickrPhoto::factory()->create([
                'sizes' => [
                    'size' => [
                        [
                            'url' => $this->faker->url,
                            'label' => 'Original',
                            'media' => 'photo',
                            'width' => '1920',
                            'height' => '1080',
                            'source' => $this->faker->url
                        ]
                    ]
                ]
            ])->id,
            'state_code' => FlickrDownloadItem::STATE_INIT
        ];
    }
}
