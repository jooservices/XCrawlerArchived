<?php

use App\Core\Database\Migration\Helpers\StateMigrate;
use App\Models\FlickrAlbum;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SeedForeginKeyStates extends StateMigrate
{
    protected array $states =
        [
            [
                'reference_code' => FlickrAlbum::STATE_INIT,
                'entity' => FlickrAlbum::class,
                'state' => 'new',
            ],
            [
                'reference_code' => FlickrAlbum::STATE_INFO_FAILED,
                'entity' => FlickrAlbum::class,
                'state' => 'info-failed',
            ],
            [
                'reference_code' => FlickrAlbum::STATE_PHOTOS_COMPLETED,
                'entity' => FlickrAlbum::class,
                'state' => 'photo-completed',
            ],
            [
                'reference_code' => FlickrAlbum::STATE_PHOTOS_FAILED,
                'entity' => FlickrAlbum::class,
                'state' => 'photo-failed',
            ],

        ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        parent::up();

        Schema::table('flickr_albums', function (Blueprint $table) {
            $table->foreign('state_code')->references('reference_code')->on('states');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
