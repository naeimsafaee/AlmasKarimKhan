<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->unsignedBigInteger('image_id')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->unsignedBigInteger('group_category_id')->nullable();
            $table->timestamps();
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->foreign('image_id')->references('id')->on('images')->onDelete('cascade');
            $table->foreign('group_category_id')->references('id')->on('group_categories')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('categories');
    }
}
