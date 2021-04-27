<?php

namespace Tests\Unit\Command;

use App\Models\XCityIdol;
use App\Services\Client\CrawlerClientResponse;
use App\Services\Client\Domain\ResponseInterface;
use App\Services\Client\XCrawlerClient;
use App\Services\Crawler\XCityIdolCrawler;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

class XCityIdolTest extends TestCase
{
    use RefreshDatabase;

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

    public function test_idol_pages_command()
    {
        $this->mocker->method('get')->willReturn($this->getSuccessfulMockedResponse('items.html'));
        app()->instance(XCrawlerClient::class, $this->mocker);
        $this->crawler = app(XCityIdolCrawler::class);

        $this->artisan('jav:xcity-idol-pages');

        $this->assertDatabaseHas('x_city_idol_pages', ['url' => '/idol/?kana=か']);
        // @TODO Another links
    }

    public function test_idols_command()
    {
        $this->mocker->method('get')->willReturn($this->getSuccessfulMockedResponse('items.html'));
        app()->instance(XCrawlerClient::class, $this->mocker);
        $this->crawler = app(XCityIdolCrawler::class);

        $this->artisan('jav:xcity-idol-pages');
        $this->artisan('jav:xcity-idols');

        $this->assertEquals(30, XCityIdol::forState(XCityIdol::STATE_INIT)->count());
        $this->assertDatabaseHas('x_city_idols', ['url' => 'detail/5628/']);
    }

    public function test_idol_command()
    {
        $this->mocker->method('get')->willReturn($this->getSuccessfulMockedResponse('idol.html'));
        app()->instance(XCrawlerClient::class, $this->mocker);
        $this->crawler = app(XCityIdolCrawler::class);

        $idol = XCityIdol::factory()->create([
            'url' => 'detail/5628/',
            'state_code' => XCityIdol::STATE_INIT
        ]);

        $this->artisan('jav:xcity-idol');
        $sampleItem = json_decode($this->getFixture('idol.json'), true);

        unset($sampleItem['birthday']);
        unset($sampleItem['url']);
        unset($sampleItem['height']);
        unset($sampleItem['breast']);
        unset($sampleItem['waist']);
        unset($sampleItem['hips']);
        $this->assertDatabaseHas('x_city_idols', $sampleItem);
    }

}
