<?php

namespace App\Jav\Tests\Unit\Models;

use App\Events\MovieCreated;
use App\Models\Idol;
use App\Models\Movie;
use App\Models\Onejav;
use App\Models\Tag;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class MovieTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Event::fake([MovieCreated::class]);
    }

    public function test_movie_has_many_onejav()
    {
        /**
         * @var Onejav $onejav
         */
        $onejav = Onejav::factory()->create();
        $movie = $onejav->movie;

        $this->assertDatabaseHas('movies', [
            'dvd_id' => $onejav->dvd_id,
            'description' => $onejav->description,
            'is_downloadable' => true,
        ]);

        $this->assertEquals($onejav->dvd_id, $movie->dvd_id);
    }

    public function test_movie_has_many_tags()
    {
        /**
         * @var Movie $movie
         */
        $movie = Movie::factory()->create();
        $tag = Tag::factory()->create();
        $idol = Idol::factory()->create();

        $movie->tags()->syncWithoutDetaching([$tag->id]);
        $movie->idols()->syncWithoutDetaching([$idol->id]);

        $this->assertEquals($tag->name, $movie->tags()->first()->name);
        $this->assertEquals($idol->name, $movie->idols()->first()->name);
    }
}
