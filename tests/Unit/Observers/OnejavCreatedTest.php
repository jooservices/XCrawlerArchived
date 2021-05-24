<?php

namespace Tests\Unit\Observers;

use App\Mail\WordPressPost;
use App\Models\Idol;
use App\Models\Movie;
use App\Models\Onejav;
use App\Models\Tag;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class OnejavCreatedTest extends TestCase
{
    public function test_create_onejav()
    {
        /**
         * @var Onejav $onejav
         */
        $onejav = Onejav::factory()->create();

        $this->assertDatabaseHas('movies', [
            'cover' => $onejav->cover,
            'dvd_id' => $onejav->dvd_id,
            'description' => $onejav->description,
            'is_downloadable' => true,
        ]);

        $movie = Movie::findByDvdId($onejav->getDvdId());
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

        foreach ($onejav->tags as $tag) {
            $this->assertDatabaseHas('tags', ['name' => $tag]);
            $tag = Tag::findByName($tag);
            $this->assertDatabaseHas('tag_movie', [
                'movie_id' => $movie->id,
                'tag_id' => $tag->id
            ]);
        }
        foreach ($onejav->actresses as $actress) {
            $this->assertDatabaseHas('idols', ['name' => $actress]);
            $idol = Idol::findByName($actress);
            $this->assertDatabaseHas('idol_movie', [
                'movie_id' => $movie->id,
                'idol_id' => $idol->id
            ]);
        }

        Mail::assertSent(WordPressPost::class);
    }
}
