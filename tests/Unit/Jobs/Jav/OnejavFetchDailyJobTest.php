<?php

namespace Tests\Unit\Jobs\Jav;

use App\Jobs\Jav\OnejavFetchDailyJob;
use App\Services\Client\CrawlerClientResponse;
use App\Services\Client\Domain\ResponseInterface;
use App\Services\Client\XCrawlerClient;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

class OnejavFetchDailyJobTest extends TestCase
{
    private MockObject|XCrawlerClient $mocker;

    public function setUp(): void
    {
        parent::setUp();
        app()->bind(ResponseInterface::class, CrawlerClientResponse::class);
        $this->mocker = $this->getMockBuilder(XCrawlerClient::class)->getMock();
        $this->mocker->method('init')->willReturnSelf();
        $this->mocker->method('setHeaders')->willReturnSelf();
        $this->mocker->method('setContentType')->willReturnSelf();
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
