<?php

namespace Tests\Unit\Jobs;

use App\Jobs\OnejavFetchDailyJob;
use App\Jobs\OnejavFetchNewJob;
use App\Models\Onejav;
use App\Models\TemporaryUrl;
use App\Services\Client\CrawlerClientResponse;
use App\Services\Client\Domain\ResponseInterface;
use App\Services\Client\XCrawlerClient;
use App\Services\Jav\OnejavService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
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

    public function test_onejav_daily_job()
    {
        Notification::fake();
        $this->mocker->method('get')->willReturn($this->getSuccessfulMockedResponse('onejav_new.html'));
        app()->instance(XCrawlerClient::class, $this->mocker);
        OnejavFetchDailyJob::dispatch(Onejav::NEW_URL);

        $this->assertEquals(10, Onejav::count());
    }

    public function test_onejav_new_job()
    {
        Notification::fake();
        $this->mocker->method('get')->willReturn($this->getSuccessfulMockedResponse('onejav_new.html'));
        app()->instance(XCrawlerClient::class, $this->mocker);
        $temporaryUrl = TemporaryUrl::factory()->create([
            'url' => $this->faker->url,
            'source' => OnejavService::SOURCE,
            'data' => ['current_page' => 1],
            'state_code' => TemporaryUrl::STATE_INIT
        ]);
        OnejavFetchNewJob::dispatch($temporaryUrl);

        $this->assertEquals(10, Onejav::count());
    }
}
