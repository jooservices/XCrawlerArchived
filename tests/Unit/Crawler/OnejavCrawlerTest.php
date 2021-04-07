<?php

namespace Tests\Unit\Crawler;

use App\Models\Onejav;
use App\Services\Client\CrawlerClientResponse;
use App\Services\Client\Domain\ResponseInterface;
use App\Services\Client\XCrawlerClient;
use App\Services\Crawler\OneJavCrawler;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

class OnejavCrawlerTest extends TestCase
{
    private OneJavCrawler $crawler;
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
        $this->crawler = app(OneJavCrawler::class);

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
        $this->crawler = app(OneJavCrawler::class);

        $items = $this->crawler->getItems(Onejav::NEW_URL);
        $this->assertNull($items);
    }
}
