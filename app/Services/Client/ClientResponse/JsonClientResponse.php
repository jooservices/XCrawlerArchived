<?php

namespace App\Services\Client\ClientResponse;

use App\Services\Client\Domain\ResponseInterface;

class JsonClientResponse extends AbstractClientResponse implements ResponseInterface
{
    public bool $responseSuccess = true;
    public int $responseCode;
    public string $responseMessage = '';
    public string $endpoint = '';
    public array $request = [];
    public array $headers = [];
    public string $body = '';

    public function loadData()
    {
        $data = json_decode($this->body, true);
        if ($data['result'] !== 'success') {
            $this->responseSuccess = false;
        }

        $this->data = $data['reply'];
    }
}
