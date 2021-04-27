<?php

namespace Tests\Unit\Crawler;

use App\Services\Client\CrawlerClientResponse;
use App\Services\Client\Domain\ResponseInterface;
use App\Services\Client\XCrawlerClient;
use App\Services\Crawler\Item;
use App\Services\Crawler\R18Crawler;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

class R18CrawlerTest extends TestCase
{
    private R18Crawler $crawler;
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

    public function test_get_item()
    {
        $this->mocker->method('get')->willReturn($this->getSuccessfulMockedResponse('r18_item.html'));
        app()->instance(XCrawlerClient::class, $this->mocker);
        $this->crawler = app(R18Crawler::class);
        $item = $this->crawler->getItem($this->faker->url);

        $this->assertInstanceOf(Item::class, $item);
        $expectedItem = json_decode($this->getFixture('r18_item.json'));

        foreach ($expectedItem as $key => $value) {
            switch ($key) {
                case'release_date':
                    $this->assertEquals('2021-04-09', $item->get('release_date')->format('Y-m-d'));
                    break;
                case 'url':
                    break;
                default:
                    $this->assertEquals($item->get($key), $value);
            }
        }
    }

    public function test_get_item_with_different_release_date_format()
    {
        $this->mocker->method('get')->willReturn($this->getSuccessfulMockedResponse('r18_item_2.html'));
        app()->instance(XCrawlerClient::class, $this->mocker);
        $this->crawler = app(R18Crawler::class);
        $item = $this->crawler->getItem($this->faker->url);

        $this->assertInstanceOf(Item::class, $item);
    }

    public function test_get_item_links()
    {
        $this->mocker->method('get')->willReturn($this->getSuccessfulMockedResponse('r18_items.html'));
        app()->instance(XCrawlerClient::class, $this->mocker);
        $this->crawler = app(R18Crawler::class);
        $links = $this->crawler->getItemLinks($this->faker->url);

        $this->assertEquals(30, $links->count());
    }

    public function test_get_pages_count()
    {
        $this->mocker->method('get')->willReturn($this->getSuccessfulMockedResponse('r18_items.html'));
        app()->instance(XCrawlerClient::class, $this->mocker);
        $this->crawler = app(R18Crawler::class);
        $pagesCount = $this->crawler->getPages($this->faker->url);

        $this->assertEquals(1667, $pagesCount);
    }
}
