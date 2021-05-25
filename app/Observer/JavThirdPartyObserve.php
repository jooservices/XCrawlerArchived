<?php

namespace App\Observer;

use App\Jobs\CreateMovieJob;
use Illuminate\Database\Eloquent\Model;

class JavThirdPartyObserve
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
