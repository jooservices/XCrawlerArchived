<?php

namespace App\Jav\Events;

use App\Core\EventSourcing\RecordedEvent;
use App\Models\AbstractJavMovie;
use App\Models\Movie;
use Illuminate\Database\Eloquent\Model;

/**
 * Whenever movie is created and idols / tags are synced
 * @package App\Events
 */
class MovieCreated implements RecordedEvent
{
    public AbstractJavMovie $model;
    public Movie $movie;

    public function __construct(AbstractJavMovie $model, Movie $movie)
    {
        $this->model = $model;
        $this->movie = $movie;
    }

    public function getCategory(): string
    {
        return 'movie.created';
    }

    public function getPayload(): array
    {
        return [
            'type' => $this->model->getMorphClass(),
            'type_id' => $this->model->id,
            'movie_id' => $this->movie->id,
            'tags' => $this->movie->tags,
            'idols' => $this->movie->idols,
        ];
    }

    public function getAggregate(): Model
    {
        return $this->model;
    }
}
