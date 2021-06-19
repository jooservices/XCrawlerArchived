<?php

namespace Database\Factories;

use App\Models\XCrawlerLog;
use Illuminate\Database\Eloquent\Factories\Factory;

class XCrawlerLogFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = XCrawlerLog::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'url' => $this->faker->url,
            'payload' => [],
            'response' => '',
            'source' => $this->faker->uuid,
            'succeed' => true
        ];
    }
}
