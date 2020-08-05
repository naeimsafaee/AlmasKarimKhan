<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnitArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unit_articles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('unit_id')->index();
            $table->string('title');
            $table->string('slug')->nullable();
            $table->longText('body');
            $table->unsignedBigInteger('image_id')->index();
            $table->unsignedBigInteger('thumbnail_image_id')->nullable();
            $table->text('seo_desc')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
        Schema::table('unit_articles', function (Blueprint $table) {

            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
            $table->foreign('image_id')->references('id')->on('images')->onDelete('cascade');
            $table->foreign('thumbnail_image_id')->references('id')->on('images')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('unit_articles');
    }
}
