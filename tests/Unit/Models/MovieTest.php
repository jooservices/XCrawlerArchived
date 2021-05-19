<?php

namespace Tests\Unit\Models;

use App\Models\Movie;
use App\Models\Onejav;
use Tests\TestCase;

class MovieTest extends TestCase
{
    public function test_onejav()
    {
        /**
         * @var Onejav $onejav
         */
        $onejav = Onejav::factory()->create();
        /**
         * @var Movie $movie
         */
        $movie = Movie::factory()->create();
        $this->assertNotEquals($movie->id, $onejav->movie()->first()->id);
    }
}
