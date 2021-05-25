<?php

namespace Tests\Unit\Observers;

use App\Mail\WordPressIdolPost;
use App\Models\Idol;
use App\Models\Movie;
use App\Models\R18;
use App\Models\Tag;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class R18CreatedTest extends TestCase
{
    public function test_create_r18()
    {
        Mail::fake();
        $r18 = R18::factory()->create();

        $this->assertDatabaseHas('movies', [
            'cover' => $r18->cover,
            'dvd_id' => $r18->dvd_id,
            'is_downloadable' => false,
        ]);

        $movie = Movie::findByDvdId($r18->getDvdId());
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

        foreach ($r18->tags as $tag) {
            $this->assertDatabaseHas('tags', ['name' => $tag]);
            $tag = Tag::findByName($tag);
            $this->assertDatabaseHas('tag_movie', [
                'movie_id' => $movie->id,
                'tag_id' => $tag->id
            ]);
        }
        foreach ($r18->actresses as $actress) {
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
