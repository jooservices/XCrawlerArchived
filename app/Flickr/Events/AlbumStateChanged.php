<?php

namespace App\Flickr\Events;

use App\Models\FlickrAlbum;

class AlbumStateChanged
{
    public function __construct(public FlickrAlbum $album)
    {
    }
}
