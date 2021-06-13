<?php

namespace App\Listeners;

use App\Events\MovieCreated;
use App\Mail\WordPressMoviePost;
use App\Models\WordPressPost;
use App\Notifications\FavoritedMovie;
use Illuminate\Support\Facades\Mail;

class MovieEventSubscriber
{
    public function movieCreated(MovieCreated $event)
    {
        $movie = $event->movie;

        // Trigger notifications
        foreach ($movie->tags()->cursor() as $tag) {
            if ($tag->favorite()->exists()) {
                $movie->notify(new FavoritedMovie());
                break;
            }
        }

        foreach ($movie->idols()->cursor() as $idol) {
            if ($idol->favorite()->exists()) {
                $movie->notify(new FavoritedMovie());
                break;
            }
        }

        // Do not create WordPress is we have no cover
        if (!$movie->cover) {
            return;
        }

        // Send movie post
        if (WordPressPost::where(['title' => $movie->dvd_id])->exists()) {
            return;
        }

        if (app()->environment('production')) {
            Mail::send(new WordPressMoviePost($movie));
        }

        WordPressPost::create(['title' => $movie->dvd_id]);
    }

    public function subscribe($events)
    {
        $events->listen([MovieCreated::class], self::class . '@movieCreated');
    }
}
