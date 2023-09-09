<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecentViewsTables extends Migration
{
    public function up()
    {
        Schema::create(config('recently-viewed.persist_table'), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->morphs('viewer');
            // $table->uuidMorphs('viewer');
            $table->string('type');
            $table->text('views');
            $table->timestamps();
        });
    }

    /**
    * Reverse the migrations.
    */
    public function down()
    {
        Schema::dropIfExists(config('recently-viewed.persist_table'));
    }
}
