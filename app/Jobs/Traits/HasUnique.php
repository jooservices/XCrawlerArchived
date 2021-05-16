<?php

namespace App\Jobs\Traits;

use Illuminate\Support\Carbon;

trait HasUnique
{
    /**
     * The number of seconds after which the job's unique lock will be released.
     *
     * @var int
     */
    public int $uniqueFor = 900;

    protected function getUnique(array $data): string
    {
        return serialize([$data, app()->environment('production') ? null: Carbon::now()]);
    }
}
