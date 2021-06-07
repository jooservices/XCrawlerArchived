<?php

namespace Tests\Unit\Observers;

use App\Events\MovieCreated;
use App\Mail\WordPressMoviePost;
use App\Models\Idol;
use App\Models\Movie;
use App\Models\Onejav;
use App\Models\Tag;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class OnejavObserveTest extends TestCase
{
    /**
     * @var Onejav
     */
    private Onejav $onejav;

    public function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    public function test_onejav_create_dispatch_movie_created()
    {
        Event::fake([MovieCreated::class]);
        $this->onejav = Onejav::factory()->create();
        /**
         * @var Movie $movie
         */
        $movie = Movie::factory()->create();
        $this->assertNotEquals($movie->id, $this->onejav->movie()->first()->id);

        Event::assertDispatched(MovieCreated::class);
    }

    public function test_create_onejav()
    {
        $this->onejav = Onejav::factory()->create();
        $this->assertDatabaseHas('movies', [
            'cover' => $this->onejav->cover,
            'dvd_id' => $this->onejav->dvd_id,
            'description' => $this->onejav->description,
            'is_downloadable' => true,
        ]);

        $movie = $this->onejav->movie;
        $tag = Tag::factory()->create();
        $actress = Idol::factory()->create();

        $this->assertDatabaseMissing('tag_movie', [
            'movie_id' => $movie->id,
            'tag_id' => $tag->id
        ]);
        $this->assertDatabaseMissing('idol_movie', [
            'movie_id' => $movie->id,
            'idol_id' => $actress->id
        ]);

        foreach ($this->onejav->tags as $tag) {
            $this->assertDatabaseHas('tags', ['name' => $tag]);
            $tag = Tag::findByName($tag);
            $this->assertDatabaseHas('tag_movie', [
                'movie_id' => $movie->id,
                'tag_id' => $tag->id
            ]);
        }
        foreach ($this->onejav->actresses as $actress) {
            $this->assertDatabaseHas('idols', ['name' => $actress]);
            $idol = Idol::findByName($actress);
            $this->assertDatabaseHas('idol_movie', [
                'movie_id' => $movie->id,
                'idol_id' => $idol->id
            ]);
        }

        Mail::assertQueued(WordPressMoviePost::class);
    }
}
