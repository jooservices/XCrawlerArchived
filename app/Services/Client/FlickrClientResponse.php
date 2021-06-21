<?php

namespace App\Services\Client;

use App\Services\Client\Domain\ResponseInterface;

class FlickrClientResponse implements ResponseInterface
{
    public bool $responseSuccess = true;
    public int $responseCode;
    public string $responseMessage = '';
    public string $endpoint = '';
    public array $request = [];
    public array $headers = [];
    public ?array $data;
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
     * Transform from other response
     *
     * @param FlickrClientResponse $response
     * @return $this
     */
    public function from(FlickrClientResponse $response): self
    {
        foreach (get_object_vars($response) as $property => $value) {
            $this->{$property} = $value;
        }

        return $this;
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

    public function loadData()
    {
        $this->data = json_decode($this->body, true);
        if (!$this->data) {
            $this->responseSuccess = false;
            $this->responseMessage = 'Unable to decode Flickr response';
            return;
        }

        if ($this->data['stat'] === 'fail') {
            $this->responseSuccess = false;
            $this->responseMessage = 'Request failed';
        }

        $this->data = $this->cleanTextNodes($this->data);
    }

    private function cleanTextNodes($arr)
    {
        if (!is_array($arr)) {
            return $arr;
        } elseif (count($arr) == 0) {
            return $arr;
        } elseif (count($arr) == 1 && array_key_exists('_content', $arr)) {
            return $arr['_content'];
        } else {
            foreach ($arr as $key => $element) {
                $arr[$key] = $this->cleanTextNodes($element);
            }
            return ($arr);
        }
    }
}
