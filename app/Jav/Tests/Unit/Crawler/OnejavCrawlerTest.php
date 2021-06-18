<?php

namespace App\Jav\Tests\Unit\Crawler;

use App\Models\Onejav;
use App\Services\Client\XCrawlerClient;
use App\Services\Crawler\OnejavCrawler;
use Tests\AbstractCrawlingTest;

class OnejavCrawlerTest extends AbstractCrawlingTest
{
    private OnejavCrawler $crawler;

    public function setUp(): void
    {
        parent::setUp();
        $this->fixtures = __DIR__ . '/../../Fixtures/Onejav';
    }

    public function test_get_items_on_news()
    {
        $this->mocker->method('get')->willReturn($this->getSuccessfulMockedResponse('new.html'));

        app()->instance(XCrawlerClient::class, $this->mocker);
        $this->crawler = app(OnejavCrawler::class);

        $items = $this->crawler->getItems(Onejav::NEW_URL);
        $item = $items->first()->toArray();
        $this->assertEquals(10, $items->count());

        $this->assertArrayHasKey('url', $item);
        $this->assertArrayHasKey('cover', $item);
        $this->assertArrayHasKey('dvd_id', $item);
        $this->assertArrayHasKey('size', $item);
        $this->assertArrayHasKey('date', $item);
        $this->assertArrayHasKey('tags', $item);
        $this->assertArrayHasKey('description', $item);
        $this->assertArrayHasKey('actresses', $item);
        $this->assertArrayHasKey('torrent', $item);

        $item = json_decode($this->getFixture('item.json'));

        foreach ($item as $key => $value) {
            if ($key === 'date') {
                $this->assertEquals('2021-04-07', $items->first()->get('date')->format('Y-m-d'));
                continue;
            }
            $this->assertEquals($items->first()->get($key), $value);
        }
    }

    public function test_get_items_on_news_with_invalid_datetime()
    {
        $this->mocker->method('get')->willReturn($this->getSuccessfulMockedResponse('new_datetime.html'));

        app()->instance(XCrawlerClient::class, $this->mocker);
        $this->crawler = app(OnejavCrawler::class);

        $items = $this->crawler->getItems(Onejav::NEW_URL);

        $item = $items->first()->toArray();
        $this->assertEquals(10, $items->count());

        $this->assertArrayHasKey('url', $item);
        $this->assertArrayHasKey('cover', $item);
        $this->assertArrayHasKey('dvd_id', $item);
        $this->assertArrayHasKey('size', $item);
        $this->assertArrayHasKey('date', $item);
        $this->assertArrayHasKey('tags', $item);
        $this->assertArrayHasKey('description', $item);
        $this->assertArrayHasKey('actresses', $item);
        $this->assertArrayHasKey('torrent', $item);

        $item = json_decode($this->getFixture('item.json'));

        foreach ($item as $key => $value) {
            if ($key === 'date') {
                $this->assertNull($items->first()->get('date'));
                continue;
            }
            $this->assertEquals($items->first()->get($key), $value);
        }
    }

    public function test_get_items_on_news_failed()
    {
        $this->mocker->method('get')->willReturn($this->getErrorMockedResponse('fake'));

        app()->instance(XCrawlerClient::class, $this->mocker);
        $this->crawler = app(OnejavCrawler::class);

        $items = $this->crawler->getItems(Onejav::NEW_URL);
        $this->assertEmpty($items);
    }

    public function test_get_pages_count()
    {
        $this->mocker
            ->expects($this->exactly(9))
            ->method('get')
            ->withConsecutive(
                ['page.html'],
                ['page.html', ['page' => 2]],
                ['page.html', ['page' => 3]],
                ['page.html', ['page' => 4]],
                ['page.html', ['page' => 5]],
                ['page.html', ['page' => 6]],
                ['page.html', ['page' => 7]],
                ['page.html', ['page' => 8]],
                ['page.html', ['page' => 9]]
            )
            ->willReturnOnConsecutiveCalls(
                $this->getSuccessfulMockedResponse('page.html'),
                $this->getSuccessfulMockedResponse('page_2.html'),
                $this->getSuccessfulMockedResponse('page_3.html'),
                $this->getSuccessfulMockedResponse('page_4.html'),
                $this->getSuccessfulMockedResponse('page_5.html'),
                $this->getSuccessfulMockedResponse('page_6.html'),
                $this->getSuccessfulMockedResponse('page_7.html'),
                $this->getSuccessfulMockedResponse('page_8.html'),
                $this->getSuccessfulMockedResponse('page_9.html')
            );

        app()->instance(XCrawlerClient::class, $this->mocker);
        $this->crawler = app(OnejavCrawler::class);

        $items = collect();
        $this->assertEquals(9, $this->crawler->getItemsRecursive($items, 'page.html', []));
        $this->assertEquals(84, $items->count());
    }
}
