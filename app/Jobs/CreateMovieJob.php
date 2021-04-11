<?php

namespace App\Jobs;

use App\Models\AbstractJavMovie;

use App\Models\Idol;
use App\Models\Movie;

use App\Models\MovieAttribute;
use App\Models\Tag;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateMovieJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private AbstractJavMovie $model;

    public function __construct(AbstractJavMovie $model)
    {
        $this->model = $model;
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

            MovieAttribute::firstOrCreate([
                'movie_id' => $movie->id,
                'model_type' => Tag::class,
                'model_id' => $tag->id,
            ]);
        }

        foreach ($this->model->getActresses() as $actress) {
            if (!$idol = Idol::firstOrCreate(['name' => $actress])) {
                continue;
            }

            MovieAttribute::firstOrCreate([
                'movie_id' => $movie->id,
                'model_type' => Idol::class,
                'model_id' => $idol->id,
            ]);
        }
    }
}
