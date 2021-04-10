<?php

namespace Tests\Unit\Crawler;

use App\Models\Onejav;
use App\Services\Client\CrawlerClientResponse;
use App\Services\Client\Domain\ResponseInterface;
use App\Services\Client\XCrawlerClient;
use App\Services\Crawler\OnejavCrawler;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

class OnejavCrawlerTest extends TestCase
{
    private OnejavCrawler $crawler;
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

    public function test_get_items_on_news()
    {
        $this->mocker->method('get')->willReturn($this->getSuccessfulMockedResponse('onejav_new.html'));

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

        $item = json_decode($this->getFixture('onejav_item.json'));

        foreach ($item as $key => $value) {
            if ($key === 'date') {
                $this->assertEquals('2021-04-07', $items->first()->get('date')->format('Y-m-d'));
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

    public function test_get_page_count()
    {
        $this->mocker
            ->expects($this->exactly(4))
            ->method('get')
            ->withConsecutive(
                ['onejav_page.html'],
                ['onejav_page.html',['page' => 4]],
                ['onejav_page.html', ['page' => 7]],
                ['onejav_page.html', ['page' => 8]]
            )
            ->willReturnOnConsecutiveCalls(
                $this->getSuccessfulMockedResponse('onejav_page.html'),
                $this->getSuccessfulMockedResponse('onejav_page_4.html'),
                $this->getSuccessfulMockedResponse('onejav_page_7.html'),
                $this->getSuccessfulMockedResponse('onejav_page_8.html')
            );

        app()->instance(XCrawlerClient::class, $this->mocker);
        $this->crawler = app(OnejavCrawler::class);

        $items = collect();
        $this->assertEquals(8, $this->crawler->getItemsRecursive($items, 'onejav_page.html', []));
        $this->assertEquals(33, $items->count());
    }
}
