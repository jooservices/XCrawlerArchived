<?php

namespace App\Services\Client\ClientResponse;

use App\Services\Client\Domain\ResponseInterface;

abstract class AbstractClientResponse implements ResponseInterface
{
    public bool $responseSuccess = true;
    public int $responseCode;
    public string $responseMessage = '';
    public string $endpoint = '';
    public array $request = [];
    public array $headers = [];
    public $data;
    public string $body = '';

    /**
     * Check the status of the response
     *
     * @return bool
     */
    public function isSuccessful(): bool
    {
        return $this->responseSuccess;
    }

    /**
     * Get the response message
     *
     * @return string|null
     */
    public function getResponseMessage(): ?string
    {
        return $this->responseMessage;
    }

    /**
     * Get response headers
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Get the endpoint
     *
     * @return string
     */
    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    /**
     * Get the request
     *
     * @return array
     */
    public function getRequest(): array
    {
        return $this->request;
    }

    /**
     * Set Error
     *
     * @param string $error
     *
     * @return $this
     */
    public function error(string $error = 'Error'): self
    {
        $this->responseSuccess = false;
        $this->responseMessage = $error;

        return $this;
    }

    /**
     * Set response headers
     *
     * @param array $headers
     *
     * @return $this
     */
    public function headers(array $headers = []): self
    {
        $this->headers = $headers;

        return $this;
    }

    /**
     * Cast the object to array
     *
     * @return array
     */
    public function toArray(): array
    {
        return json_decode(json_encode($this), true);
    }

    /**
     * Get the Data
     *
     * @return mixed
     */
    public function getData(): mixed
    {
        return $this->data ?? null;
    }

    /**
     * Get the Body
     *
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }
}
