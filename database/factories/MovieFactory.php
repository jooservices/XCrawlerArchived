<?php

namespace Database\Factories;

use App\Models\Movie;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class MovieFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Movie::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'cover'=> $this->faker->url,
            'sales_date' => Carbon::now(),
            'release_date'=> Carbon::now(),
            'content_id' => $this->faker->uuid,
            'dvd_id' => $this->faker->uuid,
            'description'=> $this->faker->text,
            'time' => $this->faker->numberBetween(10, 200),
            'director' => $this->faker->name,
            'studio'=> $this->faker->name,
            'label'=> $this->faker->name,
            'channel'=> $this->faker->name,
            'series'=> $this->faker->name,
            'gallery'=> [],

            'is_downloadable' => $this->faker->boolean
        ];
    }
}
