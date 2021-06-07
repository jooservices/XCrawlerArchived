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
            'title' => $this->faker->title,
        ];
    }

    public function init()
    {
        return $this->state(function (array $attributes) {
            return [
                'state_code' => FlickrPhoto::STATE_INIT,
                'sizes' => null,
            ];
        });
    }
}
