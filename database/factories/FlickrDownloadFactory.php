<?php

namespace Database\Factories;

use App\Models\FlickrDownload;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class FlickrDownloadFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = FlickrDownload::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->title,
            'path' => Str::slug($this->faker->title),
            'total' => 1,
            'state_code' => FlickrDownload::STATE_INIT
        ];
    }
}
