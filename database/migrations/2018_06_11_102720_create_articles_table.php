<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('article_category_id')->unsigned();
            $table->integer('admin_id')->unsigned()->nullable();
            $table->string('title');
            $table->longText('body')->nullable();
            $table->unsignedBigInteger('image_id')->nullable();
            $table->unsignedBigInteger('thumbnail_image_id')->nullable();
            $table->string('slug')->unique()->nullable();
            $table->text('seo_desc')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
        Schema::table('articles',function (Blueprint $table){

            $table->foreign('article_category_id')
                ->references('id')->on('article_categories')
                ->onDelete('cascade');

            $table->foreign('image_id')
                ->references('id')->on('images')
                ->onDelete('cascade');

            $table->foreign('thumbnail_image_id')
                ->references('id')->on('images')
                ->onDelete('cascade');



            $table->foreign('admin_id')
                ->references('id')->on('admins')
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
        Schema::dropIfExists('articles');
    }
}
