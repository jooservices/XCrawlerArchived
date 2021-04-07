<?php

namespace Tests\Unit\Jobs;

use App\Jobs\OnejavFetch;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class OnejavFetchTest extends TestCase
{

    public function test()
    {
        OnejavFetch::dispatch($this->faker->uuid);
    }
}
