<?php

namespace App\Jav\Tests\Feature\Command;

use App\Jav\Jobs\OnejavFetchNewJob;
use App\Models\Onejav;
use App\Models\TemporaryUrl;
use App\Services\Client\XCrawlerClient;
use Illuminate\Support\Facades\Queue;
use Tests\AbstractCrawlingTest;

class OnejavNewTest extends AbstractCrawlingTest
{
    public function setUp(): void
    {
        parent::setUp();
        Queue::fake();

        $this->fixtures = __DIR__ . '/../../Fixtures/Onejav';
        $this->mocker->method('get')->willReturn($this->getSuccessfulMockedResponse('new.html'));
        app()->instance(XCrawlerClient::class, $this->mocker);
    }

    public function test_execute_onejav_new()
    {
        $this->artisan('jav:onejav-new');

        $this->assertDatabaseCount('temporary_urls', 1);
        Queue::assertPushed(function (OnejavFetchNewJob $job) {
            $url = $job->url;
            return $url instanceof TemporaryUrl
                && $url->url === Onejav::NEW_URL
                && $url->state_code === TemporaryUrl::STATE_INIT;
        });
    }
}
