<?php

use App\Models\FlickrAlbum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

class SeedForeginKeyStates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $now = Carbon::now();
        \Illuminate\Support\Facades\DB::table('states')->insert([
            'reference_code' => FlickrAlbum::STATE_INIT,
            'entity' => FlickrAlbum::class,
            'state' => 'new',
            'created_at' => $now,
            'updated_at' => $now
        ]);

        \Illuminate\Support\Facades\DB::table('states')->insert([
            'reference_code' => FlickrAlbum::STATE_INFO_FAILED,
            'entity' => FlickrAlbum::class,
            'state' => 'info-failed',
            'created_at' => $now,
            'updated_at' => $now
        ]);

        \Illuminate\Support\Facades\DB::table('states')->insert([
            'reference_code' => FlickrAlbum::STATE_PHOTOS_PROCESSING,
            'entity' => FlickrAlbum::class,
            'state' => 'photo-processing',
            'created_at' => $now,
            'updated_at' => $now
        ]);

        \Illuminate\Support\Facades\DB::table('states')->insert([
            'reference_code' => FlickrAlbum::STATE_PHOTOS_COMPLETED,
            'entity' => FlickrAlbum::class,
            'state' => 'photo-completed',
            'created_at' => $now,
            'updated_at' => $now
        ]);

        \Illuminate\Support\Facades\DB::table('states')->insert([
            'reference_code' => FlickrAlbum::STATE_PHOTOS_FAILED,
            'entity' => FlickrAlbum::class,
            'state' => 'photo-failed',
            'created_at' => $now,
            'updated_at' => $now
        ]);

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
