<?php

namespace Database\Factories;

use App\Models\Idol;
use Illuminate\Database\Eloquent\Factories\Factory;

class IdolFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Idol::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'cover' => $this->faker->url,
            'birthday' => $this->faker->date(),
            'blood_type' => $this->faker->word,
            'city' => $this->faker->city
        ];
    }
}
