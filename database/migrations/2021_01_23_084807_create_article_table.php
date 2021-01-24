<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('article', function (Blueprint $table) {
            $table->id();            
            $table->timestamps();
            $table->string('reply_id', 30)->nullable();
            $table->string('user_id', 30)->nullable();            
            $table->string('platform', 30);
            $table->string('comment', 255);
            $table->string('report', 100)->nullable();

            $table->boolean('tag_vc_yes')->default(false);
            $table->boolean('tag_vc_no')->default(false);
            $table->boolean('tag_cooperation')->default(false);
            $table->boolean('tag_friend')->default(false);
            $table->boolean('tag_clan')->default(false);
            $table->boolean('tag_rank')->default(false);
            $table->boolean('tag_quick')->default(false);
            $table->boolean('tag_event')->default(false);
            $table->boolean('tag_seriously')->default(false);
            $table->boolean('tag_society')->default(false);
            $table->boolean('tag_student')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('article');
    }
}
