<?php

use App\Core\Database\Migration\Helpers\StateMigrate;
use App\Models\FlickrAlbum;

class SeedFlickrAlbumManualState extends StateMigrate
{
    protected array $states = [
        [
            'reference_code' => FlickrAlbum::STATE_MANUAL,
            'entity' => FlickrAlbum::class,
            'state' => 'manually',
        ],
    ];
}
