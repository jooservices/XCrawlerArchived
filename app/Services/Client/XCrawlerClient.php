<?php

namespace App\Services\Client;

use App\Services\Client\Domain\ResponseInterface;
use Campo\UserAgent;
use GuzzleHttp\MessageFormatter;

class XCrawlerClient extends AbstractClient
{
    public function init(string $name = null, array $options = [], int $maxRetries = 3, int $delayInSec = 1, int $minErrorCode = 500, string $loggingFormat = MessageFormatter::CLF): Domain\ClientInterface
    {
        app()->bind(ResponseInterface::class, CrawlerClientResponse::class);
        $this->contentType ='application/x-www-form-urlencoded';
        $this->setHeaders([
            'User-Agent' => UserAgent::random([
                'device_type' => 'Desktop'
            ]),
        ]);

        return parent::init($name ?? 'xcrawler', $options, $maxRetries, $delayInSec, $minErrorCode, $loggingFormat);
    }
}
