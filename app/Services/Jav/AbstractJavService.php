<?php

namespace App\Services\Jav;

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
}
