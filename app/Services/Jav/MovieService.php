<?php

namespace App\Services\Jav;

use App\Models\Movie;
use App\Models\Tag;

class MovieService
{
    public function favoriteTags(Movie $movie, Tag $tag)
    {
        //$movie->notify(new FavoritedMovie());
    }

    public function favoriteIdols(Movie $movie)
    {
        //$movie->notify(new FavoritedMovie());
    }
}
