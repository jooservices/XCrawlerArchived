<?php

namespace Tests\Unit\Jobs;

use App\Jobs\OnejavFetchJob;
use App\Models\Onejav;
use App\Models\XCrawlerLog;
use App\Services\Client\CrawlerClientResponse;
use App\Services\Client\Domain\ResponseInterface;
use App\Services\Client\XCrawlerClient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

class OnejavFetchTest extends TestCase
{
    private MockObject|XCrawlerClient $mocker;
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        app()->bind(ResponseInterface::class, CrawlerClientResponse::class);
        $this->mocker = $this->getMockBuilder(XCrawlerClient::class)->getMock();
        $this->mocker->method('init')->willReturnSelf();
        $this->mocker->method('setHeaders')->willReturnSelf();
        $this->mocker->method('setContentType')->willReturnSelf();
        $this->fixtures = __DIR__ . '/../../Fixtures/Onejav';
    }

    public function test_onejavfetch_job()
    {
        $this->mocker->method('get')->willReturn($this->getSuccessfulMockedResponse('onejav_new.html'));
        app()->instance(XCrawlerClient::class, $this->mocker);
        OnejavFetchJob::dispatch(Onejav::NEW_URL);

        $this->assertDatabaseHas('x_crawler_logs', [
            'url' => Onejav::NEW_URL,
            'source' => 'onejav.new'
        ]);
    }
}
