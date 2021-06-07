<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateR18Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('r18', function (Blueprint $table) {
            $table->id();
            $table->string('url')->unique();
            $table->string('cover')->nullable();
            $table->string('title')->nullable();
            $table->dateTime('release_date')->nullable();
            $table->integer('runtime')->nullable();
            $table->string('director')->nullable();
            $table->string('studio')->nullable();
            $table->string('label')->nullable();
            $table->json('tags')->nullable();
            $table->json('actresses')->nullable();
            $table->string('channel')->nullable();
            $table->string('content_id')->index()->nullable();
            $table->string('dvd_id')->index()->nullable();
            $table->string('series')->nullable();
            $table->string('languages')->nullable();
            $table->string('sample')->nullable();
            $table->json('gallery')->nullable();

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
        Schema::dropIfExists('r18');
    }
}
