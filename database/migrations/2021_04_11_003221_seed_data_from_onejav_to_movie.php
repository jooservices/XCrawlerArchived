<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SeedDataFromOnejavToMovie extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $now = Carbon::now();
        foreach (DB::table('onejav')->cursor() as $onejav) {
            DB::table('movies')->insert([
                'cover' => $onejav->cover,
                'dvd_id' => $onejav->dvd_id,
                'description' => $onejav->description,
                'is_downloadable' => true,
                'created_at' => $now,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
