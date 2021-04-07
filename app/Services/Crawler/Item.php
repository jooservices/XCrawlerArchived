<?php

namespace App\Services\Crawler;

class Item
{
    public function toArray(): array
    {
        return get_object_vars($this);
    }

    public function get(string $attribute, $default = null)
    {
        return $this->{$attribute} ?? $default;
    }
}
