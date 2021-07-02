<?php

namespace Database\Factories;

use App\Models\NowRestaurant;
use Illuminate\Database\Eloquent\Factories\Factory;

class NowRestaurantFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = NowRestaurant::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'id' => $this->faker->numberBetween(),
            'address' => $this->faker->address
        ];
    }
}
