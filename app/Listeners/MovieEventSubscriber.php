<?php

namespace App\Listeners;

use App\Events\MovieCreated;
use App\Notifications\FavoritedMovie;

class MovieEventSubscriber
{
    public function movieCreated(MovieCreated $event)
    {
        // Trigger notifications
        foreach ($event->movie->tags()->cursor() as $tag) {
            if ($tag->favorite()->exists()) {
                $event->movie->notify(new FavoritedMovie());
                break;
            }
        }

        foreach ($event->movie->idols()->cursor() as $idol) {
            if ($idol->favorite()->exists()) {
                $event->movie->notify(new FavoritedMovie());
                break;
            }
        }
    }

    public function subscribe($events)
    {
        $events->listen([MovieCreated::class], self::class . '@movieCreated');
    }
}
