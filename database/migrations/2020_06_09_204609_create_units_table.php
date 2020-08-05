<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('last_name');
            $table->string('email');
            $table->string('password');
            $table->unsignedBigInteger('unit_status_id')->index();
            $table->unsignedBigInteger('slide_image_id')->index()->nullable();
            $table->longText('description');
            $table->unsignedBigInteger('image_id')->index();
            $table->unsignedBigInteger('unit_category_id')->index();
            $table->unsignedBigInteger('vitrin_image_id')->index()->nullable();
            $table->string('slug');
            $table->unsignedBigInteger('pluck_id')->index();
            $table->unsignedBigInteger('phone_number');
            $table->unsignedInteger('postal_code');
            $table->timestamps();
        });
        Schema::table('units', function (Blueprint $table) {
            $table->foreign('unit_status_id')->references('id')->on('unit_statuses')->onDelete('cascade');
            $table->foreign('slide_image_id')->references('id')->on('images')->onDelete('cascade');
            $table->foreign('image_id')->references('id')->on('images')->onDelete('cascade');
            $table->foreign('unit_category_id')->references('id')->on('unit_categories')->onDelete('cascade');
            $table->foreign('vitrin_image_id')->references('id')->on('images')->onDelete('cascade');
            $table->foreign('pluck_id')->references('id')->on('plucks')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('units');
    }
}
