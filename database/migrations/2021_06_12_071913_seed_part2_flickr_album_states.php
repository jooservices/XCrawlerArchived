<?php

use App\Core\Database\Migration\Helpers\StateMigrate;
use App\Models\FlickrAlbum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SeedPart2FlickrAlbumStates extends StateMigrate
{
    protected array $states = [
        [
            'reference_code' => FlickrAlbum::STATE_INIT,
            'entity' => FlickrAlbum::class,
            'state' => 'new',
            'description' => 'Album was created'
        ],
        [
            'reference_code' => FlickrAlbum::STATE_INFO_FAILED,
            'entity' => FlickrAlbum::class,
            'state' => 'info-failed',
            'description' => 'Can not get album information'
        ],
        [
            'reference_code' => FlickrAlbum::STATE_PHOTOS_COMPLETED,
            'entity' => FlickrAlbum::class,
            'state' => 'photos-completed',
            'description' => 'Fetched all photos of album'
        ],
        [
            'reference_code' => FlickrAlbum::STATE_PHOTOS_FAILED,
            'entity' => FlickrAlbum::class,
            'state' => 'photos-failed',
            'description' => 'Can not get album photos'
        ],
        [
            'reference_code' => FlickrAlbum::STATE_PHOTOS_PROCESSING,
            'entity' => FlickrAlbum::class,
            'state' => 'photos-processing',
            'description' => 'Fetching photos of album'
        ],
        [
            'reference_code' => FlickrAlbum::STATE_MANUAL,
            'entity' => FlickrAlbum::class,
            'state' => 'manual',
            'description' => 'Album was created manually'
        ]
    ];
}
