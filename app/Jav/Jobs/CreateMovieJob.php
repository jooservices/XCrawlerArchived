<?php

namespace App\Jav\Jobs;

use App\Jav\Events\MovieCreated;
use App\Models\AbstractJavMovie;
use App\Models\Idol;
use App\Models\Movie;
use App\Models\Tag;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Event;

/**
 * Create movie from 3rd
 * @package App\Jobs
 */
class CreateMovieJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * The number of seconds after which the job's unique lock will be released.
     *
     * @var int
     */
    public $uniqueFor = 900;

    public function __construct(private AbstractJavMovie $model)
    {
    }

    /**
     * The unique ID of the job.
     *
     * @return string
     */
    public function uniqueId()
    {
        return $this->model->getDvdId();
    }

    public function handle()
    {
        $fillable = app(Movie::class)->getFields();
        $modelAttributes = $this->model->getAttributes();

        $attributes = [];

        foreach ($fillable as $key) {
            if (!isset($modelAttributes[$key])) {
                continue;
            }

            $attributes[$key] = $modelAttributes[$key];
        }

        $attributes['is_downloadable'] = $this->model->isDownloadable();
        $attributes['dvd_id'] = $this->model->getDvdId();
        $attributes['name'] = $this->model->getName();

        /**
         * @var Movie $movie
         */
        $movie = Movie::firstOrCreate(
            ['dvd_id' => $attributes['dvd_id']],
            $attributes
        );

        if (!$movie) {
            return;
        }

        foreach ($this->model->getTags() as $tag) {
            if (!$tag = Tag::firstOrCreate(['name' => $tag])) {
                continue;
            }

            $movie->tags()->syncWithoutDetaching([$tag->id]);
        }

        foreach ($this->model->getActresses() as $actress) {
            if (!$idol = Idol::firstOrCreate(['name' => $actress])) {
                continue;
            }

            $movie->idols()->syncWithoutDetaching([$idol->id]);
        }

        Event::dispatch(new MovieCreated($this->model, $movie));
    }
}
