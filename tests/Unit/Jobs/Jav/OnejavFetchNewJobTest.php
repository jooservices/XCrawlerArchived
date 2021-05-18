<?php

namespace Tests\Unit\Jobs\Jav;

use App\Jobs\Jav\OnejavFetchNewJob;
use App\Models\Onejav;
use App\Models\TemporaryUrl;
use App\Services\Client\XCrawlerClient;
use App\Services\Jav\OnejavService;
use Tests\AbstractCrawlingTest;

class OnejavFetchNewJobTest extends AbstractCrawlingTest
{
    private TemporaryUrl $url;

    public function setUp(): void
    {
        parent::setUp();
        $this->fixtures = __DIR__ . '/../../../Fixtures/Onejav';
    }

    public function test_fetch_new_job()
    {
        $this->mocker->method('get')->willReturn($this->getSuccessfulMockedResponse('new.html'));
        app()->instance(XCrawlerClient::class, $this->mocker);
        $this->url = TemporaryUrl::factory()->create([
            'url' => Onejav::NEW_URL,
            'source' => OnejavService::SOURCE,
            'data' => ['current_page' => 1],
            'state_code' => TemporaryUrl::STATE_INIT
        ]);

        OnejavFetchNewJob::dispatch($this->url);
        $this->assertDatabaseCount('onejav', 10);
        $this->url->refresh();

        $this->assertEquals(2, $this->url->data['current_page']);
    }

    public function test_fetch_new_job_with_empty_data()
    {
        $this->mocker->method('get')->willReturn($this->getSuccessfulMockedResponse('new.html'));
        app()->instance(XCrawlerClient::class, $this->mocker);
        $this->url = TemporaryUrl::factory()->create([
            'url' => Onejav::NEW_URL,
            'source' => OnejavService::SOURCE,
            'data' => [],
            'state_code' => TemporaryUrl::STATE_INIT
        ]);

        OnejavFetchNewJob::dispatch($this->url);
        $this->assertDatabaseCount('onejav', 10);
        $this->url->refresh();

        $this->assertEquals(2, $this->url->data['current_page']);
    }

    public function test_fetch_new_job_with_last_page()
    {
        $this->mocker->method('get')->willReturn($this->getSuccessfulMockedResponse('new.html'));
        app()->instance(XCrawlerClient::class, $this->mocker);
        $temporaryUrl = TemporaryUrl::factory()->create([
            'url' => Onejav::NEW_URL,
            'source' => OnejavService::SOURCE,
            'data' => ['current_page' => 7340],
            'state_code' => TemporaryUrl::STATE_INIT
        ]);

        OnejavFetchNewJob::dispatch($temporaryUrl);
        $this->assertDatabaseCount('onejav', 10);
        $temporaryUrl->refresh();

        $this->assertEquals(7340, $temporaryUrl->data['current_page']);
        $this->assertEquals(TemporaryUrl::STATE_COMPLETED, $temporaryUrl->state_code);
    }

    public function test_cant_fetch_new_job()
    {
        $this->mocker->method('get')->willReturn($this->getErrorMockedResponse('failed.html'));
        app()->instance(XCrawlerClient::class, $this->mocker);
        $temporaryUrl = TemporaryUrl::factory()->create([
            'url' => Onejav::NEW_URL,
            'source' => OnejavService::SOURCE,
            'data' => ['current_page' => 7340],
            'state_code' => TemporaryUrl::STATE_INIT
        ]);

        OnejavFetchNewJob::dispatch($temporaryUrl);
        $this->assertDatabaseCount('onejav', 0);
        $this->assertEquals(7340, $temporaryUrl->data['current_page']);
        $this->assertEquals(TemporaryUrl::STATE_INIT, $temporaryUrl->state_code);
    }

    /**
     * @TODO Test when job failed
     */
}
