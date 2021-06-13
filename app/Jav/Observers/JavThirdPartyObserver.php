<?php

namespace App\Jav\Observers;

use App\Jav\Jobs\CreateMovieJob;
use Illuminate\Database\Eloquent\Model;

class JavThirdPartyObserver
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
