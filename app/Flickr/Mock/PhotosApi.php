<?php

namespace App\Flickr\Mock;

class PhotosApi extends AbstractMocker
{
    public function getSizes()
    {
        return $this->getResponse('photos_sizes')['sizes'];
    }
}
