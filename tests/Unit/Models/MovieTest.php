<?php

namespace Tests\Unit\Models;

use App\Events\MovieCreated;
use App\Models\Movie;
use App\Models\Onejav;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class MovieTest extends TestCase
{
    public function test_onejav()
    {
        Event::fake([MovieCreated::class]);

        /**
         * @var Onejav $onejav
         */
        $onejav = Onejav::factory()->create();
        /**
         * @var Movie $movie
         */
        $movie = Movie::factory()->create();
        $this->assertNotEquals($movie->id, $onejav->movie()->first()->id);

        Event::assertDispatched(MovieCreated::class);
    }
}
