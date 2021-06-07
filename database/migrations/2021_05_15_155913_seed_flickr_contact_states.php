<?php

use App\Core\Database\Migration\Helpers\StateMigrate;
use App\Models\FlickrContact;

class SeedFlickrContactStates extends StateMigrate
{
    protected array $states = [
        [
            'reference_code' => FlickrContact::STATE_INIT,
            'entity' => FlickrContact::class,
            'state' => 'new',
        ],
        [
            'reference_code' => FlickrContact::STATE_INFO_COMPLETED,
            'entity' => FlickrContact::class,
            'state' => 'info-completed',
        ],
        [
            'reference_code' => FlickrContact::STATE_INFO_FAILED,
            'entity' => FlickrContact::class,
            'state' => 'info-failed',
        ],
        [
            'reference_code' => FlickrContact::STATE_PHOTOS_PROCESSING,
            'entity' => FlickrContact::class,
            'state' => 'photos-processing',
        ],
        [
            'reference_code' => FlickrContact::STATE_PHOTOS_COMPLETED,
            'entity' => FlickrContact::class,
            'state' => 'photos-completed',
        ],
        [
            'reference_code' => FlickrContact::STATE_PHOTOS_FAILED,
            'entity' => FlickrContact::class,
            'state' => 'photos-failed',
        ],
        [
            'reference_code' => FlickrContact::STATE_ALBUM_PROCESSING,
            'entity' => FlickrContact::class,
            'state' => 'album-processing',
        ],
        [
            'reference_code' => FlickrContact::STATE_ALBUM_COMPLETED,
            'entity' => FlickrContact::class,
            'state' => 'album-completed',
        ],
        [
            'reference_code' => FlickrContact::STATE_ALBUM_FAILED,
            'entity' => FlickrContact::class,
            'state' => 'album-failed',
        ],
    ];
}
