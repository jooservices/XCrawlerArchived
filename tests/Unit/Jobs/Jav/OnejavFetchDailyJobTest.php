<?php

namespace Tests\Unit\Jobs\Jav;

use App\Jobs\Jav\OnejavFetchDailyJob;
use App\Models\Onejav;
use App\Services\Client\XCrawlerClient;
use App\Services\Crawler\OnejavCrawler;
use Tests\AbstractCrawlingTest;

class OnejavFetchDailyJobTest extends AbstractCrawlingTest
{
    public function setUp(): void
    {
        parent::setUp();
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
    }

    public function test_fetch_daily_job_no_duplicate()
    {
        $this->mocker->method('get')->willReturn($this->getSuccessfulMockedResponse('new.html'));
        app()->instance(XCrawlerClient::class, $this->mocker);
        OnejavFetchDailyJob::dispatch();
        OnejavFetchDailyJob::dispatch();
        $items = app(OnejavCrawler::class)->getItems(Onejav::NEW_URL);
        $this->assertEquals($items->count(), Onejav::all()->count());
    }

    /**
     * @TODO Test when job failed
     */
}
