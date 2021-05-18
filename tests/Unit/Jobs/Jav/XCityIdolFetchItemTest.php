<?php

namespace Tests\Unit\Jobs\Jav;

use App\Jobs\Jav\XCityIdolFetchItem;
use App\Models\Idol;
use App\Models\TemporaryUrl;
use App\Services\Client\CrawlerClientResponse;
use App\Services\Client\Domain\ResponseInterface;
use App\Services\Client\XCrawlerClient;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

class XCityIdolFetchItemTest extends TestCase
{
    private MockObject|XCrawlerClient $mocker;

    private TemporaryUrl $url;

    public function setUp(): void
    {
        parent::setUp();
        app()->bind(ResponseInterface::class, CrawlerClientResponse::class);
        $this->mocker = $this->getMockBuilder(XCrawlerClient::class)->getMock();
        $this->mocker->method('init')->willReturnSelf();
        $this->mocker->method('setHeaders')->willReturnSelf();
        $this->mocker->method('setContentType')->willReturnSelf();
        $this->fixtures = __DIR__ . '/../../../Fixtures/XCity';
        $this->url = TemporaryUrl::factory()->create(['url' => $this->faker->url, 'source' => $this->faker->uuid, 'state_code' => $this->faker->uuid]);
    }

    public function test_xcity_idol_fetch_item_job()
    {
        $this->mocker->method('get')->willReturn($this->getSuccessfulMockedResponse('idol.html'));
        app()->instance(XCrawlerClient::class, $this->mocker);
        XCityIdolFetchItem::dispatch($this->url);
        $sampleItem = json_decode($this->getFixture('idol.json'), true);

        unset($sampleItem['birthday']);
        unset($sampleItem['url']);
        unset($sampleItem['height']);
        unset($sampleItem['breast']);
        unset($sampleItem['waist']);
        unset($sampleItem['hips']);
        $this->assertDatabaseHas('idols', $sampleItem);

        $this->url->refresh();
        $this->assertEquals(TemporaryUrl::STATE_COMPLETED, $this->url->state_code);
    }

    public function test_xcity_idol_fetch_item_job_no_duplicated()
    {
        $this->mocker->method('get')->willReturn($this->getSuccessfulMockedResponse('idol.html'));
        app()->instance(XCrawlerClient::class, $this->mocker);
        $sampleItem = json_decode($this->getFixture('idol.json'), true);
        $idol = Idol::factory()->create(['name' => $sampleItem['name']]);
        XCityIdolFetchItem::dispatch($this->url);

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

        $this->assertDatabaseCount('idols', 1);
        $this->assertDatabaseHas('idols', $sampleItem);
        $this->url->refresh();
        $this->assertEquals(TemporaryUrl::STATE_COMPLETED, $this->url->state_code);
    }

    public function test_xcity_idol_fetch_item_job_failed()
    {
        $this->mocker->method('get')->willReturn($this->getErrorMockedResponse('idol.html'));
        app()->instance(XCrawlerClient::class, $this->mocker);
        XCityIdolFetchItem::dispatch($this->url);

        $this->assertDatabaseCount('idols', 0);
        $this->url->refresh();
        $this->assertEquals(TemporaryUrl::STATE_FAILED, $this->url->state_code);
    }
}
