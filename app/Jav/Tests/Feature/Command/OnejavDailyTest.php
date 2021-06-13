<?php

namespace App\Jav\Tests\Feature\Command;

use App\Jav\Jobs\OnejavFetchDailyJob;
use Illuminate\Support\Facades\Queue;
use Tests\AbstractCrawlingTest;

class OnejavDailyTest extends AbstractCrawlingTest
{
    public function test_execute_onejav_daily()
    {
        Queue::fake();

        $this->artisan('jav:onejav-daily');
        Queue::assertPushed(OnejavFetchDailyJob::class);
    }
}
