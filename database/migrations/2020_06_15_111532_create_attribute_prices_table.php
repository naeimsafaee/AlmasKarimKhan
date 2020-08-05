<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttributePricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attribute_prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attribute_id')->index();
            $table->unsignedBigInteger('price');
            $table->string('api_url')->nullable();
            $table->timestamps();
        });

        Schema::table('attribute_prices', function (Blueprint $table) {
            $table->foreign('attribute_id')->references('id')->on('attributes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attribute_prices');
    }
}
