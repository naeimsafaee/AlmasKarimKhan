<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnitArticleKeywordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unit_article_keywords', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('unit_article_id')->index();
            $table->string('keyword');
            $table->timestamps();
        });
        Schema::table('unit_article_keywords', function (Blueprint $table) {
            $table->foreign('unit_article_id')->references('id')->on('unit_articles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('unit_article_keywords');
    }
}
