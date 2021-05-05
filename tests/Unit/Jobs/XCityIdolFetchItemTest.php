<?php


namespace Tests\Unit\Jobs;

use App\Jobs\XCityIdolFetchItem;
use App\Models\Idol;
use App\Models\TemporaryUrl;
use App\Services\Client\CrawlerClientResponse;
use App\Services\Client\Domain\ResponseInterface;
use App\Services\Client\XCrawlerClient;
use App\Services\Crawler\XCityIdolCrawler;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

class XCityIdolFetchItemTest extends TestCase
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

    public function test_fetch_item()
    {
        $this->mocker->method('get')->willReturn($this->getSuccessfulMockedResponse('idol.html'));
        app()->instance(XCrawlerClient::class, $this->mocker);
        $this->crawler = app(XCityIdolCrawler::class);

        $url = TemporaryUrl::factory()->create(['url' => $this->faker->url, 'source' => $this->faker->uuid, 'state_code' => $this->faker->uuid]);
        XCityIdolFetchItem::dispatch($url);
        $sampleItem = json_decode($this->getFixture('idol.json'), true);

        unset($sampleItem['birthday']);
        unset($sampleItem['url']);
        unset($sampleItem['height']);
        unset($sampleItem['breast']);
        unset($sampleItem['waist']);
        unset($sampleItem['hips']);
        $this->assertDatabaseHas('idols', $sampleItem);
    }

    public function test_fetch_item_no_duplicated()
    {
        $this->mocker->method('get')->willReturn($this->getSuccessfulMockedResponse('idol.html'));
        app()->instance(XCrawlerClient::class, $this->mocker);
        $this->crawler = app(XCityIdolCrawler::class);
        $sampleItem = json_decode($this->getFixture('idol.json'), true);

        $idol = Idol::factory()->create([
            'name' => $sampleItem['name']
        ]);

        $url = TemporaryUrl::factory()->create(['url' => $this->faker->url, 'source' => $this->faker->uuid, 'state_code' => $this->faker->uuid]);
        XCityIdolFetchItem::dispatch($url);

        $idol->refresh();

        unset($sampleItem['birthday']);
        unset($sampleItem['url']);
        unset($sampleItem['height']);
        unset($sampleItem['breast']);
        unset($sampleItem['waist']);
        unset($sampleItem['hips']);
        foreach ($sampleItem as $key => $value) {
            $this->assertEquals($value, $idol->{$key});
        }

        $this->assertDatabaseHas('idols', $sampleItem);
    }

    public function test_fetch_item_update_duplicate()
    {
        $this->mocker->method('get')->willReturn($this->getSuccessfulMockedResponse('idol.html'));
        app()->instance(XCrawlerClient::class, $this->mocker);
        $this->crawler = app(XCityIdolCrawler::class);

        $url = TemporaryUrl::factory()->create(['url' => $this->faker->url, 'source' => $this->faker->uuid, 'state_code' => $this->faker->uuid]);
        $sampleItem = json_decode($this->getFixture('idol.json'), true);
        $idol = Idol::factory()->create([
            'name' => $sampleItem['name']
        ]);

        XCityIdolFetchItem::dispatch($url);

        unset($sampleItem['birthday']);
        unset($sampleItem['url']);
        unset($sampleItem['height']);
        unset($sampleItem['breast']);
        unset($sampleItem['waist']);
        unset($sampleItem['hips']);

        $this->assertDatabaseHas(
            'idols',
            [
                'id' => $idol->id,
                'name' => $sampleItem['name'],
                'cover' => $sampleItem['cover'],
                'favorite' => $sampleItem['favorite'],
                'blood_type' => $sampleItem['blood_type'],
                'city' => $sampleItem['city'],
            ]
        );
    }
}
