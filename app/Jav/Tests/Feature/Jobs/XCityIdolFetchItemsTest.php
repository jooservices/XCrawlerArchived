<?php

namespace App\Jav\Tests\Feature\Jobs;

use App\Jav\Jobs\XCityIdolFetchItems;
use App\Models\TemporaryUrl;
use App\Services\Client\XCrawlerClient;
use Tests\AbstractXCityTest;

class XCityIdolFetchItemsTest extends AbstractXCityTest
{
    private TemporaryUrl $url;

    public function setUp(): void
    {
        parent::setUp();
        $this->mocker->method('get')->willReturn($this->getSuccessfulMockedResponse('items.html'));
    }

    public function test_xcity_idol_fetch_items_job_with_one_page()
    {
        $this->url = TemporaryUrl::factory()->create([
            'url' => $this->faker->url, 'source' => $this->faker->uuid, 'state_code' => $this->faker->uuid,
            'data' => [
                'current_page' => 1,
                'pages' => 1,
                'payload' => [
                    'url' => $this->faker->url,
                ]
            ]
        ]);
        app()->instance(XCrawlerClient::class, $this->mocker);
        XCityIdolFetchItems::dispatch($this->url);

        /**
         * @TODO Check state is INIT
         */
        $this->assertDatabaseCount('temporary_urls', 31);

        $this->url = $this->url->refresh();
        $this->assertEquals(TemporaryUrl::STATE_COMPLETED, $this->url->state_code);
    }

    public function test_xcity_idol_fetch_items_job_with_multi_pages()
    {
        $this->url = TemporaryUrl::factory()->create([
            'url' => $this->faker->url, 'source' => $this->faker->uuid, 'state_code' => $this->faker->uuid,
            'data' => [
                'current_page' => 1,
                'pages' => 10,
                'payload' => [
                    'url' => $this->faker->url,
                ]
            ]
        ]);
        app()->instance(XCrawlerClient::class, $this->mocker);
        XCityIdolFetchItems::dispatch($this->url);

        $this->url = $this->url->refresh();

        $this->assertDatabaseCount('temporary_urls', 31);
        $this->assertEquals(2, $this->url->data['current_page']);
        $this->assertNotEquals(TemporaryUrl::STATE_COMPLETED, $this->url->state_code);
    }
}
