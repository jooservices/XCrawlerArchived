<?php

namespace Tests\Unit\Jobs\Jav;

use App\Events\Jav\OnejavDailyCompletedEvent;
use App\Jobs\Jav\OnejavFetchDailyJob;
use App\Models\Onejav;
use App\Services\Client\XCrawlerClient;
use App\Services\Crawler\OnejavCrawler;
use Illuminate\Support\Facades\Event;
use Tests\AbstractCrawlingTest;

class OnejavFetchDailyJobTest extends AbstractCrawlingTest
{
    public function setUp(): void
    {
        parent::setUp();
        Event::fake();
        $this->fixtures = __DIR__ . '/../../../Fixtures/Onejav';
    }

    public function test_fetch_new_job()
    {
        $this->mocker->method('get')->willReturn($this->getSuccessfulMockedResponse('new.html'));
        app()->instance(XCrawlerClient::class, $this->mocker);
        OnejavFetchDailyJob::dispatch();
        $this->assertDatabaseCount('onejav', 10);
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
        $this->mocker->method('get')->willReturn($this->getSuccessfulMockedResponse('new.html'));
        app()->instance(XCrawlerClient::class, $this->mocker);
        OnejavFetchDailyJob::dispatch();
        OnejavFetchDailyJob::dispatch();
        $items = app(OnejavCrawler::class)->getItems(Onejav::NEW_URL);
        $this->assertEquals($items->count(), Onejav::all()->count());
        Event::assertDispatched(OnejavDailyCompletedEvent::class);
    }

    /**
     * @TODO Test when job failed
     */
}
