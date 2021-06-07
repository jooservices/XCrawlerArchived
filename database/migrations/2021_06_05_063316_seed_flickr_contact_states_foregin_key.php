<?php

use App\Models\FlickrContact;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
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
        $now = Carbon::now();
        DB::table('states')->updateOrInsert([
            'reference_code' => FlickrContact::STATE_MANUAL,
        ], [
            'entity' => FlickrContact::class,
            'state' => 'manual',
            'created_at' => $now,
            'updated_at' => $now
        ]);
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
