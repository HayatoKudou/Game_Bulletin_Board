<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApexPlayerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('apex_player', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('platformSlug')->nullable();
            $table->integer('platformUserId')->nullable();
            $table->string('avatarUrl', 100)->nullable();
            $table->integer('level')->nullable();
            $table->integer('kills')->nullable();
            $table->integer('damage')->nullable();
            $table->string('rankScore', 10)->nullable();
            $table->string('rankScore_iconUrl', 10)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('apex_player');
    }
}
