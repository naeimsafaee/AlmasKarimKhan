<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAttributeOptionIdToAttributePricesTable extends Migration{
    /**
     * Run the migrations.
     * @return void
     */
    public function up(){
        Schema::table('attribute_prices', function(Blueprint $table){
            $table->unsignedBigInteger("attribute_option_id")->nullable();
        });
        Schema::table('attribute_prices', function (Blueprint $table) {
            $table->foreign('attribute_option_id')->references('id')->on('attribute_options')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down(){
        Schema::table('attribute_prices', function(Blueprint $table){
            //
        });
    }
}
