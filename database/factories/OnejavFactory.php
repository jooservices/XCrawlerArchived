<?php

namespace Database\Factories;

use App\Models\Onejav;
use Illuminate\Database\Eloquent\Factories\Factory;

class OnejavFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Onejav::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'url' => $this->faker->url,
            'cover'=> $this->faker->url,
            'dvd_id' => $this->faker->uuid,
            'size' => $this->faker->randomFloat(2, 0.5, 10.56),
            'date' => $this->faker->date(Onejav::DAILY_FORMAT),
            'tags' => $this->faker->randomElements(),
            'description' => $this->faker->text,
            'actresses' => $this->faker->randomElements(),
            'torrent' => $this->faker->url,
        ];
    }
}
