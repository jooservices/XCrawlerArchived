<?php

namespace Tests\Unit\Jobs;

use App\Jobs\R18FetchItemJob;
use App\Models\Movie;
use App\Services\Client\CrawlerClientResponse;
use App\Services\Client\Domain\ResponseInterface;
use App\Services\Client\XCrawlerClient;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

class R18FetchItemTest extends TestCase
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
        $this->fixtures = __DIR__ . '/../../Fixtures/R18';
    }

    public function test_r18_fetch_item_job()
    {
        $this->mocker->method('get')->willReturn($this->getSuccessfulMockedResponse('r18_item.html'));
        app()->instance(XCrawlerClient::class, $this->mocker);

        R18FetchItemJob::dispatch($this->faker->url, 'r18.released');
        $sampleItem = json_decode($this->getFixture('r18_item.json'), true);
        $tags = $sampleItem['tags'];
        $actresses = $sampleItem['actresses'];
        unset($sampleItem['url']);
        unset($sampleItem['tags']);
        unset($sampleItem['actresses']);
        unset($sampleItem['release_date']);
        unset($sampleItem['gallery']);

        $this->assertDatabaseHas('r18', $sampleItem);

        // Movie id
        $movie = Movie::where(['dvd_id' => $sampleItem['dvd_id']])->first();
        $this->assertNotNull($movie->id);

        $this->assertEquals($tags, $movie->tags->pluck('name')->toArray());
        $this->assertEquals($actresses, $movie->idols->pluck('name')->toArray());
    }
}
