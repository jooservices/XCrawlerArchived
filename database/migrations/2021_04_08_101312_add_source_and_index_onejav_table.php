<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddSourceAndIndexOnejavTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('onejavs')) {
            Schema::rename('onejavs', 'onejav');
        }

        Schema::table('onejav', function (Blueprint $table) {
            $table->string('source')->after('torrent');
            $table->index('dvd_id');
        });

        DB::table('onejav')->update(['source' => 'new']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('onejav')) {
            Schema::rename('onejav', 'onejavs');
        }
        Schema::table('onejav', function (Blueprint $table) {
            $table->dropColumn('source');
            $table->dropIndex('dvd_id');
        });
    }
}
