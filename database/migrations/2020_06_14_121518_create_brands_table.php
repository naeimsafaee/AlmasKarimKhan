<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBrandsTable extends Migration{
    /**
     * Run the migrations.
     * @return void
     */
    public function up(){
        Schema::create('brands', function(Blueprint $table){
            $table->id();
            $table->string("name");
            $table->unsignedBigInteger("image_id");
            $table->text("url")->nullable();
            $table->unsignedBigInteger("unit_id")->nullable();
            $table->timestamps();
        });
        Schema::table('brands', function (Blueprint $table) {
            $table->foreign('image_id')->references('id')->on('images')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('unit_id')->references('id')->on('units')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down(){
        Schema::dropIfExists('brands');
    }
}
