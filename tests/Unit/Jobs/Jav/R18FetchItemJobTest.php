<?php

namespace Tests\Unit\Jobs\Jav;

use App\Jobs\Jav\R18FetchItemJob;
use App\Models\Movie;
use App\Services\Client\XCrawlerClient;
use Tests\AbstractCrawlingTest;

class R18FetchItemJobTest extends AbstractCrawlingTest
{
    public function setUp(): void
    {
        parent::setUp();
        $this->fixtures = __DIR__ . '/../../../Fixtures/R18';
    }

    public function test_r18_fetch_item_job()
    {
        $this->mocker->method('get')->willReturn($this->getSuccessfulMockedResponse('item.html'));
        app()->instance(XCrawlerClient::class, $this->mocker);
        R18FetchItemJob::dispatch($this->faker->url);

        $expectedItem = json_decode($this->getFixture('item.json'), true);
        $tags = $expectedItem['tags'];
        $actresses = $expectedItem['actresses'];
        unset($expectedItem['url']);
        unset($expectedItem['tags']);
        unset($expectedItem['actresses']);
        unset($expectedItem['release_date']);
        unset($expectedItem['gallery']);

        $this->assertDatabaseCount('r18', 1);
        $this->assertDatabaseHas('r18', $expectedItem);

        // Movie id
        $movie = Movie::findByDvdId($expectedItem['dvd_id']);
        $this->assertNotNull($movie->id);

        $this->assertEquals($tags, $movie->tags->pluck('name')->toArray());
        $this->assertEquals($actresses, $movie->idols->pluck('name')->toArray());
    }

    public function test_cant_r18_fetch_item_job()
    {
        $this->mocker->method('get')->willReturn($this->getErrorMockedResponse('item.html'));
        app()->instance(XCrawlerClient::class, $this->mocker);
        R18FetchItemJob::dispatch($this->faker->url);

        $this->assertDatabaseCount('r18', 0);
    }
}
