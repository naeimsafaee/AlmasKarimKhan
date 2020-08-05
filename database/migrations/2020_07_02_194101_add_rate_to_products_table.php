<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRateToProductsTable extends Migration{
    /**
     * Run the migrations.
     * @return void
     */
    public function up(){
        Schema::table('products', function(Blueprint $table){
            $table->float("rate")->default(0)->after("category_id");
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down(){
        Schema::table('products', function(Blueprint $table){
            //
        });
    }
}
