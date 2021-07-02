<?php

namespace Database\Factories;

use App\Models\NowPromotion;
use Illuminate\Database\Eloquent\Factories\Factory;

class NowPromotionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = NowPromotion::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'id' => $this->faker->numberBetween(),
            'discount_amount'=> $this->faker->numberBetween()
        ];
    }
}
