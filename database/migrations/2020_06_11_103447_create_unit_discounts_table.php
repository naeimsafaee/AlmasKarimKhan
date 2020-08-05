<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnitDiscountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unit_discounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('unit_id')->index();
            $table->unsignedBigInteger('discount');
            $table->timestamps();
        });
        Schema::table('unit_discounts', function (Blueprint $table) {

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
        Schema::dropIfExists('unit_discounts');
    }
}
