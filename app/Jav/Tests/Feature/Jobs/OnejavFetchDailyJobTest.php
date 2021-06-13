<?php

namespace App\Jav\Tests\Feature\Jobs;

use App\Events\Jav\OnejavDailyCompletedEvent;
use App\Jobs\Jav\OnejavFetchDailyJob;
use App\Models\Onejav;
use App\Services\Client\XCrawlerClient;
use Carbon\Carbon;
use Illuminate\Support\Facades\Event;
use Tests\AbstractCrawlingTest;

class OnejavFetchDailyJobTest extends AbstractCrawlingTest
{
    public function setUp(): void
    {
        parent::setUp();
        Event::fake();
        $this->fixtures = __DIR__ . '/../../Fixtures/Onejav';
    }

    public function test_fetch_new_job()
    {
        $url = Onejav::HOMEPAGE_URL . '/' . Carbon::now()->format('Y/m/d');
        $this->mocker
            ->expects($this->exactly(3))
            ->method('get')
            ->withConsecutive(
                [$url],
                [$url, ['page' => 2]],
                [$url, ['page' => 3]],
            )
            ->willReturnOnConsecutiveCalls(
                $this->getSuccessfulMockedResponse('daily.html'),
                $this->getSuccessfulMockedResponse('daily_2.html'),
                $this->getSuccessfulMockedResponse('daily_3.html'),
            );
        app()->instance(XCrawlerClient::class, $this->mocker);

        OnejavFetchDailyJob::dispatch();
        $this->assertDatabaseCount('onejav', 23);
        Event::assertDispatched(OnejavDailyCompletedEvent::class);
    }

    public function test_cant_fetch_new_job()
    {
        $this->mocker->method('get')->willReturn($this->getErrorMockedResponse('failed.html'));
        app()->instance(XCrawlerClient::class, $this->mocker);

        OnejavFetchDailyJob::dispatch();
        $this->assertDatabaseCount('onejav', 0);
        Event::assertDispatched(OnejavDailyCompletedEvent::class);
    }

    public function test_fetch_daily_job_no_duplicate()
    {
        $this->mocker->method('get')->willReturn($this->getSuccessfulMockedResponse('daily.html'));
        app()->instance(XCrawlerClient::class, $this->mocker);

        OnejavFetchDailyJob::dispatch();
        OnejavFetchDailyJob::dispatch();

        $this->assertDatabaseCount('onejav', 10);
        Event::assertDispatchedTimes(OnejavDailyCompletedEvent::class, 2);
    }
}
