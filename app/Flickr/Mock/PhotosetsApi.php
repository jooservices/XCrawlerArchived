<?php

namespace App\Flickr\Mock;

class PhotosetsApi extends AbstractMocker
{
    public function getInfo()
    {
        return $this->getResponse('photosets_info')['photoset'];
    }

    public function getPhotos()
    {
        return $this->getResponse('photosets_photos')['photoset'];
    }

    public function getList()
    {
        return $this->getResponse('photosets_list')['photosets'];
    }
}
