<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnitGalleriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unit_galleries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('unit_id')->index();
            $table->unsignedBigInteger('image_id')->index();
            $table->timestamps();
        });
        Schema::table('unit_galleries', function (Blueprint $table) {

            $table->foreign('image_id')->references('id')->on('images')->onDelete('cascade');
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('unit_galleries');
    }
}
