<?php

namespace Tests\Unit\Jobs\Jav;

use App\Jobs\Jav\OnejavFetchDailyJob;
use App\Services\Client\XCrawlerClient;
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

    /**
     * @TODO Test when job failed
     */
}
