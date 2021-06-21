<?php

namespace App\Flickr\Mock;

class PeopleApi extends AbstractMocker
{
    public function getPhotos()
    {
        return $this->getResponse('people_photos')['photos'];
    }
}
