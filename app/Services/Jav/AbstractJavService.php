<?php

namespace App\Services\Jav;

use App\Models\XCrawlerLog;
use Illuminate\Support\Collection;

abstract class AbstractJavService
{
    protected function getPayload(Collection $items): array
    {
        return array_merge_recursive(
            ['items' => $items->map(function ($item) {
                return $item->get('url');
            })],
            ['count' => $items->count()]
        );
    }

    protected function log(string $url, array $payload, string $source, bool $succeed = true)
    {
        XCrawlerLog::create([
            'url' => $url,
            'payload' => $payload,
            'source' => $source,
            'succeed' => $succeed
        ]);
    }

    protected function succeed(string $url, array $payload, string $source)
    {
        $this->log($url, $payload, $source);
    }

    protected function failed(string $url, array $payload, string $source)
    {
        $this->log($url, $payload, $source, false);
    }
}
