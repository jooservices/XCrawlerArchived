<?php

namespace App\Flickr\Events;

use App\Models\FlickrContact;

class ContactStateChanged
{
    public function __construct(public FlickrContact $contact)
    {
    }
}
