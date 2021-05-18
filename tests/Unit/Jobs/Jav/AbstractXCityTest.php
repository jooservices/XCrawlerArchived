<?php

namespace Tests\Unit\Jobs\Jav;

use App\Services\Client\CrawlerClientResponse;
use App\Services\Client\Domain\ResponseInterface;
use App\Services\Client\XCrawlerClient;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

abstract class AbstractXCityTest extends TestCase
{
    protected MockObject|XCrawlerClient $mocker;

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
}
