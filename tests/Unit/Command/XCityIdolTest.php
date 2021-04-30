<?php

namespace Tests\Unit\Command;

use App\Models\TemporaryUrl;
use App\Models\XCityIdolPage;
use App\Services\Client\CrawlerClientResponse;
use App\Services\Client\Domain\ResponseInterface;
use App\Services\Client\XCrawlerClient;
use App\Services\Crawler\XCityIdolCrawler;
use App\Services\TemporaryUrlService;
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
        $service = app(TemporaryUrlService::class);

        $this->artisan('jav:xcity-idol-pages');
        $this->assertEquals(9, XCityIdolPage::all()->count());
        $this->artisan('jav:xcity-idols');

        $this->assertEquals(30, $service->getItems('xcity.idol', TemporaryUrl::STATE_INIT, 100)->count());

    }

    public function test_idol_command()
    {
        $this->mocker->method('get')->willReturn($this->getSuccessfulMockedResponse('idol.html'));
        app()->instance(XCrawlerClient::class, $this->mocker);
        $this->crawler = app(XCityIdolCrawler::class);

        TemporaryUrl::factory()->create([
            'url' => $this->faker->url,
            'source' => 'xcity.idol',
            'state_code' => TemporaryUrl::STATE_INIT
        ]);

        $this->artisan('jav:xcity-idol');
        $sampleItem = json_decode($this->getFixture('idol.json'), true);

        unset($sampleItem['birthday']);
        unset($sampleItem['url']);
        unset($sampleItem['height']);
        unset($sampleItem['breast']);
        unset($sampleItem['waist']);
        unset($sampleItem['hips']);
        $this->assertDatabaseHas('idols', $sampleItem);
    }

}
