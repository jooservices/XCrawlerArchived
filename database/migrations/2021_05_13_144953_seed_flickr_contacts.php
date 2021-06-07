<?php

use App\Models\FlickrContact;
use Illuminate\Database\Migrations\Migration;

class SeedFlickrContacts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Illuminate\Support\Facades\DB::table('flickr_contacts')
            ->update([
                'state_code' => FlickrContact::STATE_INIT
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
