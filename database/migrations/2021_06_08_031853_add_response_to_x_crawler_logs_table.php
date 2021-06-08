<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddResponseToXCrawlerLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('x_crawler_logs', function (Blueprint $table) {
            $table->longText('response')->nullable()->after('payload');
            $table->string('source')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('x_crawler_logs', function (Blueprint $table) {
            $table->dropColumn('response');
            $table->string('source')->nullable(false)->change();
        });
    }
}
