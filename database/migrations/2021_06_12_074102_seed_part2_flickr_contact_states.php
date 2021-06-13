<?php

use App\Core\Database\Migration\Helpers\StateMigrate;
use App\Models\FlickrContact;

class SeedPart2FlickrContactStates extends StateMigrate
{
    protected array $states = [
        [
            'reference_code' => FlickrContact::STATE_INIT,
            'entity' => FlickrContact::class,
            'state' => 'new',
            'description' => 'Contact was created',
        ],
        [
            'reference_code' => FlickrContact::STATE_INFO_COMPLETED,
            'entity' => FlickrContact::class,
            'state' => 'info-completed',
            'description' => 'Fetched contact information',
        ],
        [
            'reference_code' => FlickrContact::STATE_INFO_FAILED,
            'entity' => FlickrContact::class,
            'state' => 'info-failed',
            'description' => 'Can not get contact information',
        ],
        [
            'reference_code' => FlickrContact::STATE_PHOTOS_PROCESSING,
            'entity' => FlickrContact::class,
            'state' => 'photos-processing',
            'description' => 'Getting contact photos',
        ],
        [
            'reference_code' => FlickrContact::STATE_PHOTOS_COMPLETED,
            'entity' => FlickrContact::class,
            'state' => 'photos-completed',
            'description' => 'Fetched all photos of contact',
        ],
        [
            'reference_code' => FlickrContact::STATE_PHOTOS_FAILED,
            'entity' => FlickrContact::class,
            'state' => 'photos-failed',
            'description' => 'Can not get contact photos',
        ],
        [
            'reference_code' => FlickrContact::STATE_ALBUM_PROCESSING,
            'entity' => FlickrContact::class,
            'state' => 'album-processing',
            'description' => 'Getting contact albums',
        ],
        [
            'reference_code' => FlickrContact::STATE_ALBUM_COMPLETED,
            'entity' => FlickrContact::class,
            'state' => 'album-completed',
            'description' => 'Fetch all albums of contact',
        ],
        [
            'reference_code' => FlickrContact::STATE_ALBUM_FAILED,
            'entity' => FlickrContact::class,
            'state' => 'album-failed',
            'description' => 'Can not get contact albums',
        ],
    ];
}
