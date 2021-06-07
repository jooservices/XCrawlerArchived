<?php

namespace Database\Factories;

use App\Models\FlickrContact;
use Illuminate\Database\Eloquent\Factories\Factory;

class FlickrContactFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = FlickrContact::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'nsid' => $this->faker->uuid,
            'username' => $this->faker->name,
            'ispro' => $this->faker->boolean,
            'state_code' => FlickrContact::STATE_INIT
        ];
    }
}
