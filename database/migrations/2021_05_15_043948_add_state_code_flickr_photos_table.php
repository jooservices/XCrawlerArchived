<?php

use App\Models\FlickrPhoto;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStateCodeFlickrPhotosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('flickr_photos', function (Blueprint $table) {
            $table->string('state_code')->default(FlickrPhoto::STATE_INIT)->index()->after('sizes');
        });

        \Illuminate\Support\Facades\DB::table('flickr_photos')
            ->whereNotNull('sizes')
            ->update(['state_code' => FlickrPhoto::STATE_INIT]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('flickr_photos', function (Blueprint $table) {
            $table->dropColumn('state_code');
        });
    }
}
