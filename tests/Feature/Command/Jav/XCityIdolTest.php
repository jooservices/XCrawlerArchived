<?php

namespace Tests\Feature\Command\Jav;

use App\Models\TemporaryUrl;
use App\Services\Client\XCrawlerClient;
use App\Services\Jav\XCityIdolService;
use App\Services\TemporaryUrlService;
use Tests\AbstractXCityTest;

class XCityIdolTest extends AbstractXCityTest
{
    public function test_idols_command()
    {
        $this->mocker->method('get')->willReturn($this->getSuccessfulMockedResponse('items.html'));
        app()->instance(XCrawlerClient::class, $this->mocker);
        $service = app(TemporaryUrlService::class);

        $this->artisan('jav:xcity-idols');
        $this->assertEquals(9, $service->getItems(XCityIdolService::SOURCE)->count());
        $this->assertEquals(30, $service->getItems(XCityIdolService::SOURCE_IDOL, TemporaryUrl::STATE_INIT, 100)->count());

        // Test whenever we completed 1 page
        $temporaryUrl = TemporaryUrl::where(['source' => XCityIdolService::SOURCE])->first();
        $temporaryUrl->updateData(['current_page' => $temporaryUrl->data['pages']]);
        $this->artisan('jav:xcity-idols');
        $temporaryUrl->refresh();
        // This URL is completed
        $this->assertEquals(TemporaryUrl::STATE_COMPLETED, $temporaryUrl->state_code);

        // Process again will create new TemporaryUrl
        TemporaryUrl::where(['source' => XCityIdolService::SOURCE])->update(['state_code' => TemporaryUrl::STATE_COMPLETED]);

        $this->artisan('jav:xcity-idols');
        // 9 Init items
        $this->assertEquals(9, $service->getItems(XCityIdolService::SOURCE)->count());
        // And 9 Completed items
        $this->assertEquals(9, $service->getItems(XCityIdolService::SOURCE, TemporaryUrl::STATE_COMPLETED)->count());
    }

    public function test_idol_command()
    {
        $this->mocker->method('get')->willReturn($this->getSuccessfulMockedResponse('idol.html'));
        app()->instance(XCrawlerClient::class, $this->mocker);

        TemporaryUrl::factory()->create([
            'url' => $this->faker->url,
            'source' => XCityIdolService::SOURCE_IDOL,
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
