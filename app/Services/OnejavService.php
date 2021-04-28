<?php

namespace App\Services;

use App\Models\Onejav;

class OnejavService
{
    public function create(string $url, array $data, string $source = 'new'): Onejav
    {
        $data['source'] = $source;
        return Onejav::firstOrCreate(['url' => $url], $data);
    }
}
