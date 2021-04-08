<?php

namespace Tests\Unit\Command;

use App\Jobs\OnejavFetchJob;
use App\Models\Onejav;
use App\Services\Client\CrawlerClientResponse;
use App\Services\Client\Domain\ResponseInterface;
use App\Services\Client\XCrawlerClient;
use App\Services\Crawler\OnejavCrawler;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

class OnejavNewTest extends TestCase
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
        $this->fixtures = __DIR__ . '/../../Fixtures/Onejav';
    }

    public function test_onejav_new_command()
    {
        //Queue::fake();
        $this->mocker->method('get')->willReturn($this->getSuccessfulMockedResponse('onejav_new.html'));

        app()->instance(XCrawlerClient::class, $this->mocker);
        $crawler = app(OnejavCrawler::class);
        $items = $crawler->getItems(Onejav::NEW_URL);

        $this->artisan('jav:onejav-new');

//        Queue::assertPushed(function (OnejavFetchJob $job) {
//            return $job->page === 1 && $job->url === Onejav::NEW_URL;
//        });

        $data = json_decode($this->getFixture('onejav_item.json'), true);
        unset($data['tags']);
        unset($data['actresses']);
        unset($data['date']);

        $this->assertDatabaseHas('onejavs', $data);
        $this->assertEquals($items->count(), Onejav::all()->count());
        $this->assertEquals(2, Cache::get('onejav-news-page'));

        // No duplicate
        $this->artisan('jav:onejav-new');
        $this->assertEquals($items->count(), Onejav::all()->count());
    }

    public function test_onejav_new_command_job()
    {
        Queue::fake();
        $this->mocker->method('get')->willReturn($this->getSuccessfulMockedResponse('onejav_new.html'));

        app()->instance(XCrawlerClient::class, $this->mocker);

        $this->artisan('jav:onejav-new');

        Queue::assertPushed(function (OnejavFetchJob $job) {
            return $job->page === 1 && $job->url === Onejav::NEW_URL;
        });
    }
}
