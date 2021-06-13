<?php

namespace App\Flickr\Events;

use App\Models\FlickrAlbum;

class AlbumCreated
{
    public FlickrAlbum $album;

    public function __construct(FlickrAlbum $album)
    {
        $this->album = $album;
    }
}
