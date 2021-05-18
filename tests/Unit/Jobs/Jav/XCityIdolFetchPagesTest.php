<?php

namespace Tests\Unit\Jobs\Jav;

use App\Jobs\Jav\XCityIdolFetchPages;
use App\Models\TemporaryUrl;
use App\Services\Client\XCrawlerClient;

class XCityIdolFetchPagesTest extends AbstractXCityTest
{
    public function test_xcity_idol_fetch_pages_job()
    {
        $this->mocker->method('get')->willReturn($this->getSuccessfulMockedResponse('items.html'));
        app()->instance(XCrawlerClient::class, $this->mocker);
        XCityIdolFetchPages::dispatch($this->faker->uuid);

        $this->assertDatabaseHas('temporary_urls', [
            'source' => 'xcity_idols',
            'state_code' => TemporaryUrl::STATE_INIT
        ]);

        $temporary = TemporaryUrl::first();
        $this->assertEquals(110, $temporary->data['pages']);
        $this->assertEquals(30, $temporary->data['payload']['num']);
    }
}
