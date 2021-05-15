<?php

use App\Models\FlickrPhoto;
use Illuminate\Database\Migrations\Migration;

class SeedStateCodeFlickrPhotosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Illuminate\Support\Facades\DB::table('flickr_photos')
            ->whereNull('sizes')
            ->update(['state_code' => FlickrPhoto::STATE_INIT]);

        \Illuminate\Support\Facades\DB::table('flickr_photos')
            ->whereNotNull('sizes')
            ->update(['state_code' => FlickrPhoto::STATE_SIZE_COMPLETED]);
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
