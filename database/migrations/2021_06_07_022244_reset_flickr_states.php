<?php

use App\Models\FlickrAlbum;
use App\Models\FlickrContact;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;

class ResetFlickrStates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Illuminate\Support\Facades\DB::table('flickr_contacts')->update([
            'state_code' => FlickrContact::STATE_INIT,
            'updated_at' => Carbon::now()
        ]);
        \Illuminate\Support\Facades\DB::table('flickr_albums')->update([
            'state_code' => FlickrAlbum::STATE_INIT,
            'updated_at' => Carbon::now()
        ]);
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
