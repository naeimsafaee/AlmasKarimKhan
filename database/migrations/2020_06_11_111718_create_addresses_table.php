<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('number');
            $table->unsignedBigInteger('province_id')->index();
            $table->unsignedBigInteger('city_id')->index();
            $table->text('address');
            $table->unsignedBigInteger('postal_code');
            $table->timestamps();
        });
        Schema::table('addresses', function (Blueprint $table) {
            $table->foreign('province_id')->references('id')->on('province')->onDelete('cascade');
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('addresses');
    }
}
