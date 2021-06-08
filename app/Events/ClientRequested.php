<?php

namespace App\Events;

use App\Services\Client\Domain\ResponseInterface;

class ClientRequested
{
    public ResponseInterface $response;

    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
    }
}
