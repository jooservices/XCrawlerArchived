<?php

namespace Tests\Unit\Command;

use App\Jobs\R18FetchItemJob;
use App\Models\R18;
use App\Models\TemporaryUrl;
use App\Services\Client\CrawlerClientResponse;
use App\Services\Client\Domain\ResponseInterface;
use App\Services\Client\XCrawlerClient;
use App\Services\Crawler\R18Crawler;
use App\Services\R18Service;
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
        /**
         * We can't mock multi params in this case.
         * Job will be testes in job test later
         */
        Queue::fake();
        $this->mocker->method('get')->willReturn($this->getSuccessfulMockedResponse('r18_items.html'));
        app()->instance(XCrawlerClient::class, $this->mocker);

        $links = app(R18Crawler::class)->getItemLinks('r18_items.html');
        $this->artisan('jav:r18-released');

        Queue::assertPushed(R18FetchItemJob::class, function ($job) use ($links) {
            return $links->search($job->url);
        });
    }

    public function test_r18_released_command_job_end_of_pages()
    {
        Queue::fake();
        $this->mocker->method('get')->willReturn($this->getSuccessfulMockedResponse('r18_items.html'));
        app()->instance(XCrawlerClient::class, $this->mocker);
        $crawler = app(R18Crawler::class);

        $temporaryUrl = TemporaryUrl::factory()->create([
            'url' => R18::MOVIE_LIST_URL,
            'source' => R18Service::SOURCE,
            'data' => [
                'pages' => $crawler->getPages(R18::MOVIE_LIST_URL),
                'current_page' => 1
            ],
            'state_code' => TemporaryUrl::STATE_INIT
        ]);

        $this->artisan('jav:r18-released');
        $temporaryUrl->refresh();
        $this->assertEquals(2, $temporaryUrl->data['current_page']);

        $temporaryUrl->updateData(['current_page' => $crawler->getPages(R18::MOVIE_LIST_URL)]);
        $this->artisan('jav:r18-released');
        $temporaryUrl->refresh();

        // This new already completed
        $this->assertEquals(TemporaryUrl::STATE_COMPLETED, $temporaryUrl->state_code);

        // Call again will create new TemporaryUrl
        $this->artisan('jav:r18-released');
        $temporaryUrl = TemporaryUrl::bySource(R18Service::SOURCE)->byState(TemporaryUrl::STATE_INIT)->first();
        $this->assertEquals(2, $temporaryUrl->data['current_page']);
    }
}
