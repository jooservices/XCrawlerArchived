<?php

namespace Tests\Unit\Command;

use App\Jobs\R18FetchItemJob;
use App\Models\R18;
use App\Models\XCrawlerLog;
use App\Services\Client\CrawlerClientResponse;
use App\Services\Client\Domain\ResponseInterface;
use App\Services\Client\XCrawlerClient;
use App\Services\Crawler\R18Crawler;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

class R18ReleasedTest extends TestCase
{
    use RefreshDatabase;

    private MockObject|XCrawlerClient $mocker;

    public function setUp(): void
    {
        parent::setUp();
        app()->bind(ResponseInterface::class, CrawlerClientResponse::class);
        $this->mocker = $this->getMockBuilder(XCrawlerClient::class)->getMock();
        $this->mocker->method('init')->willReturnSelf();
        $this->mocker->method('setHeaders')->willReturnSelf();
        $this->mocker->method('setContentType')->willReturnSelf();
        $this->fixtures = __DIR__ . '/../../Fixtures/R18';
    }

    public function test_r18_released_command_job()
    {
        Queue::fake();
        $this->mocker->method('get')->willReturn($this->getSuccessfulMockedResponse('r18_items.html'));

        app()->instance(XCrawlerClient::class, $this->mocker);

        $this->artisan('jav:r18-released');

        Queue::assertPushed(R18FetchItemJob::class);
    }

}
