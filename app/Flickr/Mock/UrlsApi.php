<?php

namespace App\Flickr\Mock;

class UrlsApi extends AbstractMocker
{
    public function lookupUser()
    {
        return $this->getResponse('urls_lookupuser')['user'];
    }
}
