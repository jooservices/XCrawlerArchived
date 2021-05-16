<?php

use App\Core\Database\Migration\Helpers\StateMigrate;
use App\Models\FlickrAlbum;

class CorrectFlickrAlbumStates extends StateMigrate
{
    protected array $states = [
        [
            'reference_code' => FlickrAlbum::STATE_PHOTOS_PROCESSING,
            'entity' => FlickrAlbum::class,
            'state' => 'photos-processing',
        ],
    ];

    public function up()
    {
        parent::up();

        \Illuminate\Support\Facades\DB::table('states')
            ->where(['entity' => FlickrAlbum::class])
            ->where(['state' => 'photo-completed'])
            ->update(['state' => 'photos-completed']);

        \Illuminate\Support\Facades\DB::table('states')
            ->where(['entity' => FlickrAlbum::class])
            ->where(['state' => 'photo-failed'])
            ->update(['state' => 'photos-failed']);
    }
}
