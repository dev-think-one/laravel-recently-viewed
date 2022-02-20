<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagesTables extends Migration
{
    public function up()
    {
        Schema::create('test_pages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->timestamps();
        });
    }

    /**
    * Reverse the migrations.
    */
    public function down()
    {
        Schema::dropIfExists('test_pages');
    }
}
