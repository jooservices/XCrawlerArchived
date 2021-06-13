<?php

namespace App\Flickr\Events;

use App\Models\FlickrContact;

class ContactCreated
{
    public FlickrContact $contact;

    public function __construct(FlickrContact $contact)
    {
        $this->contact = $contact;
    }
}
