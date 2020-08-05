<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductToImagesTable extends Migration{
    /**
     * Run the migrations.
     * @return void
     */
    public function up(){
        Schema::create('product_to_images', function(Blueprint $table){
            $table->id();
            $table->unsignedBigInteger("product_id");
            $table->unsignedBigInteger("image_id");
            $table->timestamps();
        });
        Schema::table('product_to_images', function (Blueprint $table) {
            $table->foreign('product_id')->references('id')->on('products')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('image_id')->references('id')->on('images')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down(){
        Schema::dropIfExists('product_to_images');
    }
}
