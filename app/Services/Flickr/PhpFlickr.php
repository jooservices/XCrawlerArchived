<?php

namespace App\Services\Flickr;

use App\Events\ClientRequested;
use App\Services\Client\Domain\ResponseInterface;
use Illuminate\Support\Facades\Event;

class PhpFlickr extends \Jooservices\PhpFlickr\PhpFlickr
{
    public function request($command, $args = array(), $nocache = false): array
    {
        if (!str_starts_with($command, 'flickr.')) {
            $command = 'flickr.' . $command;
        }

        // See if there's a cached response.
        $cacheKey = array_merge([$command], $args);
        $this->response = $this->getCached($cacheKey);

        if (!($this->response) || $nocache) {
            $args = array_filter($args);
            $oauthService = $this->getOauthService();
            $this->response = $oauthService->requestJson($command, 'POST', $args);
            if (!$nocache) {
                $this->cache($cacheKey, $this->response);
            }
        }

        $response = app(ResponseInterface::class);
        $response->endpoint = $command;
        $response->request = $args;
        $response->body = $this->response;
        $response->loadData();

        Event::dispatch(new ClientRequested($response));
        return $response->isSuccessful() ? $response->getData() : [];
    }
}
