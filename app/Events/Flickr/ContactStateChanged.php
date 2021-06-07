<?php

namespace App\Events\Flickr;

use App\Models\FlickrContact;

class ContactStateChanged
{
    public FlickrContact $contact;

    public function __construct(FlickrContact $contact)
    {
        $this->contact = $contact;
    }
}
