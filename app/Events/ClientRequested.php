<?php

namespace App\Events;

use App\Services\Client\Domain\ResponseInterface;

class ClientRequested
{
    public function __construct(public ResponseInterface $response)
    {
    }
}
