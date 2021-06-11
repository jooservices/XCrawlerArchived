<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddModelTypeIdFlickrDownloads extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('flickr_downloads', function (Blueprint $table) {
            $table->string('model_id')->nullable()->after('id');
            $table->string('model_type')->nullable()->after('model_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('flickr_downloads', function (Blueprint $table) {
            $table->dropColumn('model_id');
            $table->dropColumn('model_type');
        });
    }
}
