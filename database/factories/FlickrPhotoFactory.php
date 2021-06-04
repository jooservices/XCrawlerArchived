<?php

namespace Database\Factories;

use App\Models\FlickrContact;
use App\Models\FlickrPhoto;
use Illuminate\Database\Eloquent\Factories\Factory;

class FlickrPhotoFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = FlickrPhoto::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'id' => $this->faker->numberBetween(1,100),
            'owner' => FlickrContact::factory()->create()->nsid,
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
        ];
    }
}
