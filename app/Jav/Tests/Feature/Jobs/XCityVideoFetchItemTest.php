<?php

namespace App\Jav\Tests\Feature\Jobs;

use App\Jav\Jobs\XCityVideoFetchItem;
use App\Models\TemporaryUrl;
use App\Services\Client\XCrawlerClient;
use Tests\AbstractXCityTest;

class XCityVideoFetchItemTest extends AbstractXCityTest
{
    private TemporaryUrl $url;

    public function setUp(): void
    {
        parent::setUp();
        $this->url = TemporaryUrl::factory()->create();
    }

    public function test_xcity_video_fetch_item_test_failed()
    {
        $this->mocker->method('get')->willReturn($this->getErrorMockedResponse('items.html'));
        app()->instance(XCrawlerClient::class, $this->mocker);
        XCityVideoFetchItem::dispatch($this->url);

        $this->url->refresh();
        $this->assertEquals(TemporaryUrl::STATE_FAILED, $this->url->state_code);
    }
}
