<?php

namespace Database\Factories;

use App\Models\R18;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class R18Factory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = R18::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'url' => $this->faker->url,
            'cover' => $this->faker->url,
            'title' => $this->faker->title,
            'release_date' => Carbon::now(),
            'runtime' => null,
            'director' => $this->faker->name,
            'studio' => $this->faker->name,
            'label' => $this->faker->name,
            'channel' => $this->faker->name,
            'content_id' => $this->faker->uuid,
            'dvd_id' => $this->faker->uuid,
            'series' => $this->faker->url,
            'languages' => $this->faker->text,
            'sample' => null,
            'gallery' => null,
            'tags' => $this->faker->randomElements(),
            'actresses' => $this->faker->randomElements(),
        ];
    }
}
