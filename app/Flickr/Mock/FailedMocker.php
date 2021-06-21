<?php

namespace App\Flickr\Mock;

class FailedMocker
{
    public function __call(string $name, array $arguments)
    {
        return false;
    }
}
