<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCategoryIdToProductsTable extends Migration{
    /**
     * Run the migrations.
     * @return void
     */
    public function up(){
        Schema::table('products', function(Blueprint $table){
            $table->unsignedBigInteger("category_id")->after("unit_id");
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
