<?php

use App\Models\FlickrContact;
use Illuminate\Database\Migrations\Migration;

class SeedFlickrContactStateFcpi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Illuminate\Support\Facades\DB::table('flickr_contacts')
            ->where(['state_code' => 'FCPI'])
            ->update([
                'state_code' => FlickrContact::STATE_INFO_COMPLETED
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
