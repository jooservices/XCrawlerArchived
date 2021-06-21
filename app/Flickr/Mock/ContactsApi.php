<?php

namespace App\Flickr\Mock;

class ContactsApi extends AbstractMocker
{
    public function getList($filter = null, $page = null, $perPage = null, $sort = null)
    {
        return $this->getResponse('contacts_list' . ($page ? '_' . $page : ''));
    }
}
