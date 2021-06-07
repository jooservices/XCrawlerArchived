<?php

namespace Tests\Unit\Observers;

use App\Events\MovieCreated;
use App\Mail\WordPressIdolPost;
use App\Models\Idol;
use App\Models\Movie;
use App\Models\R18;
use App\Models\Tag;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class R18ObserveTest extends TestCase
{
    /**
     * @var R18
     */
    private mixed $r18;

    public function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    public function test_r18_create_dispatch_movie_created()
    {
        Event::fake([MovieCreated::class]);
        $this->r18 = R18::factory()->create();
        /**
         * @var Movie $movie
         */
        $movie = Movie::factory()->create();
        $this->assertNotEquals($movie->id, $this->r18->movie()->first()->id);

        Event::assertDispatched(MovieCreated::class);
    }

    public function test_create_r18()
    {
        $this->r18 = R18::factory()->create();
        $this->assertDatabaseHas('movies', [
            'cover' => $this->r18->cover,
            'dvd_id' => $this->r18->dvd_id,
            'is_downloadable' => false,
        ]);

        $movie = $this->r18->movie()->first();
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

        foreach ($this->r18->tags as $tag) {
            $this->assertDatabaseHas('tags', ['name' => $tag]);
            $tag = Tag::findByName($tag);
            $this->assertDatabaseHas('tag_movie', [
                'movie_id' => $movie->id,
                'tag_id' => $tag->id
            ]);
        }
        foreach ($this->r18->actresses as $actress) {
            $this->assertDatabaseHas('idols', ['name' => $actress]);
            $idol = Idol::findByName($actress);
            $this->assertDatabaseHas('idol_movie', [
                'movie_id' => $movie->id,
                'idol_id' => $idol->id
            ]);
        }

        Mail::assertQueued(WordPressIdolPost::class);
    }
}
