<?php

namespace App\Flickr\Events;

use App\Models\FlickrAlbum;

class AlbumCreated
{
    public function __construct(public FlickrAlbum $album)
    {
    }
}
