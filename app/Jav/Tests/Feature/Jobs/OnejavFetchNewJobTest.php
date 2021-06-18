<?php

namespace App\Jav\Tests\Feature\Jobs;

use App\Jav\Events\OnejavNewCompletedEvent;
use App\Jav\Jobs\OnejavFetchNewJob;
use App\Models\Onejav;
use App\Models\TemporaryUrl;
use App\Services\Client\XCrawlerClient;
use App\Services\Jav\OnejavService;
use Illuminate\Support\Facades\Event;
use Tests\AbstractCrawlingTest;

class OnejavFetchNewJobTest extends AbstractCrawlingTest
{
    public function setUp(): void
    {
        parent::setUp();
        $this->fixtures = __DIR__ . '/../../Fixtures/Onejav';
        Event::fake();
    }

    public function test_fetch_new_job()
    {
        $this->mocker->method('get')->willReturn($this->getSuccessfulMockedResponse('new.html'));
        app()->instance(XCrawlerClient::class, $this->mocker);

        $url = TemporaryUrl::factory()->create([
            'url' => Onejav::NEW_URL,
            'source' => OnejavService::SOURCE,
            'data' => ['current_page' => 1],
            'state_code' => TemporaryUrl::STATE_INIT
        ]);

        OnejavFetchNewJob::dispatch($url);
        $this->assertDatabaseCount('onejav', 10);
        $this->assertEquals(2, $url->refresh()->data['current_page']);
        Event::assertDispatched(OnejavNewCompletedEvent::class, function ($event) {
            $url = $event->url;
            return $url instanceof TemporaryUrl
                && $url->url === Onejav::NEW_URL
                && $url->data['current_page'] === 2;
        });
    }

    public function test_fetch_new_job_with_empty_data()
    {
        $this->mocker->method('get')->willReturn($this->getSuccessfulMockedResponse('new.html'));
        app()->instance(XCrawlerClient::class, $this->mocker);
        $url = TemporaryUrl::factory()->create([
            'url' => Onejav::NEW_URL,
            'source' => OnejavService::SOURCE,
            'data' => [],
            'state_code' => TemporaryUrl::STATE_INIT
        ]);

        OnejavFetchNewJob::dispatch($url);
        $this->assertDatabaseCount('onejav', 10);
        $url->refresh();

        $this->assertEquals(2, $url->data['current_page']);
        Event::assertDispatched(OnejavNewCompletedEvent::class, function ($event) {
            $url = $event->url;
            return $url instanceof TemporaryUrl
                && $url->url === Onejav::NEW_URL
                && $url->data['current_page'] === 2;
        });
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
        Event::assertDispatched(OnejavNewCompletedEvent::class, function ($event) {
            $url = $event->url;
            return $url instanceof TemporaryUrl
                && $url->url === Onejav::NEW_URL
                && $url->data['current_page'] === 7340
                && $url->state_code === TemporaryUrl::STATE_COMPLETED;
        });
    }

    public function test_cant_fetch_new_job()
    {
        /**
         * Crawler always return collection
         * In this case we collection is empty
         */
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
        Event::assertDispatched(OnejavNewCompletedEvent::class, function ($event) {
            $url = $event->url;
            return $url instanceof TemporaryUrl
                && $url->url === Onejav::NEW_URL
                && $url->data['current_page'] === 7340
                && $url->state_code === TemporaryUrl::STATE_COMPLETED;
        });
    }
}
