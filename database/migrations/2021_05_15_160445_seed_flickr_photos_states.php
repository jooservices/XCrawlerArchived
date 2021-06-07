<?php

use App\Core\Database\Migration\Helpers\StateMigrate;
use App\Models\FlickrPhoto;

class SeedFlickrPhotosStates extends StateMigrate
{
    protected array $states = [
        [
            'reference_code' => FlickrPhoto::STATE_INIT,
            'entity' => FlickrPhoto::class,
            'state' => 'new',
        ],
        [
            'reference_code' => FlickrPhoto::STATE_SIZE_COMPLETED,
            'entity' => FlickrPhoto::class,
            'state' => 'sizes-completed',
        ],
        [
            'reference_code' => FlickrPhoto::STATE_SIZE_FAILED,
            'entity' => FlickrPhoto::class,
            'state' => 'sizes-failed',
        ],
    ];
}
