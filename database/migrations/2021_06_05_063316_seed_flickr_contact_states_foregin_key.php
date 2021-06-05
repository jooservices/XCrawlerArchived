<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SeedFlickrContactStatesForeginKey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('flickr_contacts', function (Blueprint $table) {
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
        Schema::table('flickr_contacts', function (Blueprint $table) {
            $table->dropForeign('flickr_contacts_state_code_foreign');
        });
    }
}
