<?php

use App\Core\Database\Migration\Helpers\StateMigrate;
use App\Models\FlickrPhoto;

class SeedPart2FlickrPhotoStates extends StateMigrate
{
    protected array $states = [
        [
            'reference_code' => FlickrPhoto::STATE_INIT,
            'entity' => FlickrPhoto::class,
            'state' => 'new',
            'description' => 'Photo was created',
        ],
        [
            'reference_code' => FlickrPhoto::STATE_SIZE_FAILED,
            'entity' => FlickrPhoto::class,
            'state' => 'sizes-failed',
            'description' => 'Can not get photo sizes',
        ],
        [
            'reference_code' => FlickrPhoto::STATE_SIZE_COMPLETED,
            'entity' => FlickrPhoto::class,
            'state' => 'sizes-completed',
            'description' => 'Fetched photo sizes',
        ],
    ];
}
