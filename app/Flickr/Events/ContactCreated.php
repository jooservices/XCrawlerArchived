<?php

namespace App\Flickr\Events;

use App\Models\FlickrContact;

class ContactCreated
{
    public function __construct(public FlickrContact $contact)
    {
    }
}
