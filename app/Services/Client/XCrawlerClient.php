<?php

namespace App\Services\Client;

use App\Services\Client\Domain\ResponseInterface;
use Faker\Factory;
use Faker\Generator;
use GuzzleHttp\MessageFormatter;

class XCrawlerClient extends AbstractClient
{
    protected Generator $faker;

    public function init(string $name = null, array $options = [], int $maxRetries = 3, int $delayInSec = 1, int $minErrorCode = 500, string $loggingFormat = MessageFormatter::CLF): Domain\ClientInterface
    {
        $locale = $locale ?? config('app.faker_locale', Factory::DEFAULT_LOCALE);
        app()->make(Generator::class, ['locale' => $locale]);
        $this->faker = Factory::create($locale);

        app()->bind(ResponseInterface::class, CrawlerClientResponse::class);
        $this->contentType = 'application/x-www-form-urlencoded';
        $this->setHeaders(['User-Agent' => str_replace('Mobile ', '', $this->faker->userAgent())]);

        return parent::init($name ?? 'xcrawler', $options, $maxRetries, $delayInSec, $minErrorCode, $loggingFormat);
    }
}
