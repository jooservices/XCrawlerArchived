<?php

namespace Tests\Unit\Jobs\Jav;

use App\Jobs\Jav\XCityIdolFetchPages;
use App\Models\TemporaryUrl;
use App\Services\Client\CrawlerClientResponse;
use App\Services\Client\Domain\ResponseInterface;
use App\Services\Client\XCrawlerClient;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

class XCityIdolFetchPagesTest extends TestCase
{
    private MockObject|XCrawlerClient $mocker;

    public function setUp(): void
    {
        parent::setUp();
        app()->bind(ResponseInterface::class, CrawlerClientResponse::class);
        $this->mocker = $this->getMockBuilder(XCrawlerClient::class)->getMock();
        $this->mocker->method('init')->willReturnSelf();
        $this->mocker->method('setHeaders')->willReturnSelf();
        $this->mocker->method('setContentType')->willReturnSelf();
        $this->fixtures = __DIR__ . '/../../../Fixtures/XCity';
    }

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
