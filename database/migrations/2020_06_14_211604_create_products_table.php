<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("slug");
            $table->string("seo_desc")->nullable();
            $table->integer("count")->nullable()->default(0);
            $table->integer("price");
            $table->tinyInteger("online_price")->default(0);
            $table->integer("discount")->default(0);
            $table->text("desc")->nullable();
            $table->unsignedBigInteger("product_status_id");
            $table->unsignedBigInteger("unit_id");
            $table->timestamps();
        });
        Schema::table('products', function (Blueprint $table) {
            $table->foreign('product_status_id')->references('id')->on('product_statuses')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('unit_id')->references('id')->on('units')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
