<?php

namespace Tests\Unit\Crawler;

use App\Services\Client\CrawlerClientResponse;
use App\Services\Client\Domain\ResponseInterface;
use App\Services\Client\XCrawlerClient;
use App\Services\Crawler\XCityVideoCrawler;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

class XCityVideoCrawlerTest extends TestCase
{
    private XCityVideoCrawler $crawler;
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

    public function test_get_links()
    {
        $this->mocker->method('get')->willReturn($this->getSuccessfulMockedResponse('videos.html'));
        app()->instance(XCrawlerClient::class, $this->mocker);
        $this->crawler = app(XCityVideoCrawler::class);
        $items = $this->crawler->getItemLinks('https://xxx.xcity.jp/avod/list/', [
            'style' => 'simple',
            'from_date' => '20210101',
            'to_date' => '20210101'
        ]);

        $this->assertEquals(30, $items->count());
    }

    public function test_get_pages_count()
    {
        $this->mocker->method('get')->willReturn($this->getSuccessfulMockedResponse('videos.html'));
        app()->instance(XCrawlerClient::class, $this->mocker);
        $this->crawler = app(XCityVideoCrawler::class);
        $pages = $this->crawler->getPages('https://xxx.xcity.jp/avod/list/', [
            'style' => 'simple',
            'from_date' => '20210101',
            'to_date' => '20210101'
        ]);

        $this->assertEquals(2, $pages);
    }

    public function test_get_item()
    {
        $this->mocker->method('get')->willReturn($this->getSuccessfulMockedResponse('video.html'));
        app()->instance(XCrawlerClient::class, $this->mocker);
        $this->crawler = app(XCityVideoCrawler::class);
        $item = $this->crawler->getItem($this->faker->url);

        $expectedItem = json_decode($this->getFixture('video.json'));

        foreach ($expectedItem as $key => $value) {
            switch ($key) {
                case'sales_date':
                    $this->assertEquals('2020-05-29', $item->get('sales_date')->format('Y-m-d'));
                    break;
                case 'release_date':
                    $this->assertEquals('2021-01-02', $item->get('release_date')->format('Y-m-d'));
                    break;
                case 'url':
                    break;
                default:
                    $this->assertEquals($item->get($key), $value);
            }
        }
    }
}
