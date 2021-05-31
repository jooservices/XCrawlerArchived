<?php

namespace Database\Factories;

use App\Models\FlickrAlbum;
use App\Models\FlickrContact;
use Illuminate\Database\Eloquent\Factories\Factory;

class FlickrAlbumFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = FlickrAlbum::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'id' => $this->faker->numberBetween(1,1000),
            'owner' => FlickrContact::factory(),
            'title' => $this->faker->title,
            'description' => $this->faker->text,
            'state_code' => FlickrAlbum::STATE_INIT
        ];
    }
}
