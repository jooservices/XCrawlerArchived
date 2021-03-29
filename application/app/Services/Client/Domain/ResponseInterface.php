<?php

namespace App\Services\Client\Domain;

interface ResponseInterface
{
    public function isSuccessful(): bool;

    public function getResponseMessage(): ?string;

    public function getHeaders(): array;

    /**
     * Request endpoint
     * @return string
     */
    public function getEndpoint(): string;

    /**
     * Payload
     * @return array
     */
    public function getRequest(): array;

    public function error(string $error = 'Error'): self;

    public function headers(array $headers = []): self;

    public function toArray(): array;

    /**
     * Parsed data from body
     * @return array
     */
    public function getData(): array;

    /**
     * Raw body
     * @return string
     */
    public function getBody(): string;
}
