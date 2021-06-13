<?php

namespace App\Jav\Tests\Unit\Crawler;

use App\Services\Client\XCrawlerClient;
use App\Services\Crawler\Item;
use App\Services\Crawler\XCityIdolCrawler;
use Tests\AbstractXCityTest;

class XCityIdolCrawlerTest extends AbstractXCityTest
{
    private XCityIdolCrawler $crawler;

    public function test_get_sub_pages()
    {
        $this->mocker->method('get')->willReturn($this->getSuccessfulMockedResponse('items.html'));
        app()->instance(XCrawlerClient::class, $this->mocker);
        $this->crawler = app(XCityIdolCrawler::class);
        $this->assertEquals(9, $this->crawler->getSubPages()->count());
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
