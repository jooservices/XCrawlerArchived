<?php

namespace App\Listeners;

use App\Events\ClientRequested;
use App\Models\XCrawlerLog;

class CrawlingEventSubscriber
{
    public function createLog(ClientRequested $event)
    {
        $response = $event->response;
        XCrawlerLog::create([
            'url' => $response->getEndpoint(),
            'payload' => $response->getRequest(),
            'response' => $response->isSuccessful() ? $response->getBody() : $response->getResponseMessage(),
            'succeed' => $response->isSuccessful(),
        ]);
    }

    public function subscribe($events)
    {
        $events->listen(
            [
                ClientRequested::class,
            ],
            self::class . '@createLog'
        );
    }
}
