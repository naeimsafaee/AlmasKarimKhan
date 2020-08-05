<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserOrdersTable extends Migration{
    /**
     * Run the migrations.
     * @return void
     */
    public function up(){

        Schema::create('user_orders', function(Blueprint $table){
            $table->id();
            $table->unsignedBigInteger("product_id");
            $table->unsignedBigInteger("user_id");
            $table->unsignedBigInteger("order_status_id");
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::table('user_orders', function(Blueprint $table){
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('order_status_id')->references('id')->on('order_statuses')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down(){
        Schema::dropIfExists('user_orders');
    }
}
