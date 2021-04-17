<?php

namespace App\Observers;

use App\Jobs\CreateMovieJob;
use Illuminate\Database\Eloquent\Model;

class JavThridPartyObserver
{
    /**
     * Handle created event.
     *
     * @param Model $model
     */
    public function created(Model $model)
    {
        CreateMovieJob::dispatch($model);
    }
}
