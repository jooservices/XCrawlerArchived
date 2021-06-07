<?php

namespace App\Services\Client;

use App\Services\Client\Domain\ResponseInterface;
use GuzzleHttp\MessageFormatter;

class XCrawlerClient extends AbstractClient
{
    public function init(string $name = null, array $options = [], int $maxRetries = 3, int $delayInSec = 1, int $minErrorCode = 500, string $loggingFormat = MessageFormatter::CLF): Domain\ClientInterface
    {
        $products = [
            'Mozilla/5.0'
        ];
        $product = $products[array_rand($products)];

        $systems = [
            'Windows NT 10.0; Win64; x64'
        ];
        $system = $systems[array_rand($systems)];

        $platforms = [
            'AppleWebKit/'. mt_rand(537, 605) .'.' .mt_rand(0, 1) . '.' . mt_rand(1, 5)
        ];
        $platform = $platforms[array_rand($platforms)];

        $platformDetails = [
            '(KHTML, like Gecko)'
        ];
        $platformDetail = $platformDetails[array_rand($platformDetails)];

        $extensions = [
            'Safari/537.36'
        ];
        $extension = $extensions[array_rand($extensions)];

        $userAgent = "{$product} / ({$system}) {$platform} ({$platformDetail}) $extension";

        app()->bind(ResponseInterface::class, CrawlerClientResponse::class);
        $this->contentType = 'application/x-www-form-urlencoded';
        $this->setHeaders(['User-Agent' => $userAgent]);

        return parent::init($name ?? 'xcrawler', $options, $maxRetries, $delayInSec, $minErrorCode, $loggingFormat);
    }
}
