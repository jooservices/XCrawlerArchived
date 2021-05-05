<?php

namespace App\Services;

use App\Models\TemporaryUrl;
use Illuminate\Support\Collection;

class TemporaryUrlService
{
    public function create(string $url, string $source, array $data = []): TemporaryUrl
    {
        return TemporaryUrl::firstOrCreate([
            'url' => $url,
            'source' => $source,
            'data' => empty($data) ? null : $data
        ], [
            'state_code' => TemporaryUrl::STATE_INIT
        ]);
    }

    public function getItems(string $source, string $state = TemporaryUrl::STATE_INIT, int $limit = 10): Collection
    {
        return TemporaryUrl::forSource($source)->forState($state)->limit($limit)->get();
    }
}
