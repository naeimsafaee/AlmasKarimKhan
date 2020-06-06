<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticleKeywordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('article_keywords', function (Blueprint $table) {
            $table->bigInteger('article_id')->unsigned();
            $table->bigInteger('keyword_id')->unsigned();
        });
        Schema::table('article_keywords',function (Blueprint $table){

            $table->foreign('keyword_id')
                ->references('id')->on('keywords')
                ->onDelete('cascade');
            $table->foreign('article_id')
                ->references('id')->on('articles')
                ->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('article_keywords');
    }
}
