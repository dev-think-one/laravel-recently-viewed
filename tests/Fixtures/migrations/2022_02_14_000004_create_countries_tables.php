<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCountriesTables extends Migration
{
    public function up()
    {
        Schema::create('test_countries', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->string('title');
            $table->timestamps();
        });
    }

    /**
    * Reverse the migrations.
    */
    public function down()
    {
        Schema::dropIfExists('test_countries');
    }
}
