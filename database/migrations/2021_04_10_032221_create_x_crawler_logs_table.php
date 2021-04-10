<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateXCrawlerLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('x_crawler_logs', function (Blueprint $table) {
            $table->id();
            $table->string('url')->index();
            $table->json('payload');
            $table->string('source')->index();
            $table->boolean('succeed')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('x_crawler_logs');
    }
}
