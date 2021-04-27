<?php

namespace Tests\Unit\Crawler;

use App\Services\Client\CrawlerClientResponse;
use App\Services\Client\Domain\ResponseInterface;
use App\Services\Client\XCrawlerClient;
use App\Services\Crawler\Item;
use App\Services\Crawler\XCityIdolCrawler;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

class XCityIdolCrawlerTest extends TestCase
{
    private XCityIdolCrawler $crawler;
    private MockObject|XCrawlerClient $mocker;

    public function setUp(): void
    {
        parent::setUp();
        app()->bind(ResponseInterface::class, CrawlerClientResponse::class);
        $this->mocker = $this->getMockBuilder(XCrawlerClient::class)->getMock();
        $this->mocker->method('init')->willReturnSelf();
        $this->mocker->method('setHeaders')->willReturnSelf();
        $this->mocker->method('setContentType')->willReturnSelf();
        $this->fixtures = __DIR__ . '/../../Fixtures/XCity';
    }

    public function test_get_item()
    {
        $this->mocker->method('get')->willReturn($this->getSuccessfulMockedResponse('idol.html'));
        app()->instance(XCrawlerClient::class, $this->mocker);
        $this->crawler = app(XCityIdolCrawler::class);
        $item = $this->crawler->getItem($this->faker->url);

        $this->assertInstanceOf(Item::class, $item);
        $expectedItem = json_decode($this->getFixture('idol.json'));

        foreach ($expectedItem as $key => $value) {
            switch ($key) {
                case'birthday':
                    $this->assertEquals('1988-05-24', $item->get('birthday')->format('Y-m-d'));
                    break;
                case 'url':
                    break;
                default:
                    $this->assertEquals($item->get($key), $value);
            }
        }
    }

    public function test_get_item_links()
    {
        $this->mocker->method('get')->willReturn($this->getSuccessfulMockedResponse('items.html'));
        app()->instance(XCrawlerClient::class, $this->mocker);
        $this->crawler = app(XCityIdolCrawler::class);
        $links = $this->crawler->getItemLinks($this->faker->url);

        $this->assertEquals(30, $links->count());
    }

    public function test_get_pages_count()
    {
        $this->mocker->method('get')->willReturn($this->getSuccessfulMockedResponse('items.html'));
        app()->instance(XCrawlerClient::class, $this->mocker);
        $this->crawler = app(XCityIdolCrawler::class);
        $pagesCount = $this->crawler->getPages($this->faker->url);

        $this->assertEquals(110, $pagesCount);
    }
}