<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOnejavsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('onejavs', function (Blueprint $table) {
            $table->id();
            $table->string('url')->unique()->index();
            $table->string('cover')->nullable();
            $table->string('dvd_id');
            $table->float('size');
            $table->dateTime('date');
            $table->json('tags')->nullable();
            $table->text('description')->nullable();
            $table->json('actresses')->nullable();
            $table->string('torrent');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('onejavs');
    }
}
