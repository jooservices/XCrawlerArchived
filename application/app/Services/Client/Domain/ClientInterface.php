<?php

namespace App\Services\Client\Domain;

use App\Services\Client\ClientResponse;
use GuzzleHttp\MessageFormatter;

interface ClientInterface
{
    public function init(
        string $name = null,
        array $options = [],
        int $maxRetries = 3,
        int $delayInSec = 1,
        int $minErrorCode = 500,
        string $loggingFormat = MessageFormatter::CLF
    ): self;

    public function getResponse(): ClientResponse;

    public function setHeaders(array $headers): self;

    public function setContentType(string $contentType = 'json'): self;

    public function get(string $endpoint, array $payload = []): ClientResponse;

    public function post(string $endpoint, array $payload = []): ClientResponse;

    public function put(string $endpoint, array $payload = []): ClientResponse;

    public function patch(string $endpoint, array $payload = []): ClientResponse;

    public function delete(string $endpoint, array $payload = []): ClientResponse;

}
