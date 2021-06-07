<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIdolsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('idols', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('alias')->nullable();
            $table->date('birthday')->nullable();
            $table->string('blood_type')->nullable();
            $table->string('city')->nullable();
            $table->smallInteger('height')->nullable();
            $table->smallInteger('breast')->nullable();
            $table->smallInteger('waist')->nullable();
            $table->smallInteger('hips')->nullable();
            $table->string('cover')->nullable();
            $table->integer('favorite')->nullable();

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
        Schema::dropIfExists('idols');
    }
}
