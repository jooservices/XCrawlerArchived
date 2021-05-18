<?php

namespace App\Services\Client;

use App\Services\Client\Domain\ResponseInterface;
use GuzzleHttp\MessageFormatter;
use Illuminate\Foundation\Testing\WithFaker;

class XCrawlerClient extends AbstractClient
{
    use WithFaker;

    public function init(string $name = null, array $options = [], int $maxRetries = 3, int $delayInSec = 1, int $minErrorCode = 500, string $loggingFormat = MessageFormatter::CLF): Domain\ClientInterface
    {
        app()->bind(ResponseInterface::class, CrawlerClientResponse::class);
        $this->contentType = 'application/x-www-form-urlencoded';
        $this->setHeaders(['User-Agent' => str_replace('Mobile ', '', $this->faker->userAgent())]);

        return parent::init($name ?? 'xcrawler', $options, $maxRetries, $delayInSec, $minErrorCode, $loggingFormat);
    }
}
