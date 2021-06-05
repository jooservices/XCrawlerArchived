<?php

namespace App\Events\Flickr;

use App\Models\FlickrContact;

class ContactCreated
{
    public FlickrContact $contact;

    public function __construct(FlickrContact $contact)
    {
        $this->contact = $contact;
    }
}
