<?php

namespace App\Flickr\Events;

use App\Models\FlickrContact;

class ContactStateChanged
{
    public FlickrContact $contact;

    public function __construct(FlickrContact $contact)
    {
        $this->contact = $contact;
    }
}
