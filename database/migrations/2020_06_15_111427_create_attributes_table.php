<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttributesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attributes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('type'); //1:input - 2:select - 3:checkbox
            $table->longText('description')->nullable();
            $table->unsignedBigInteger('group_attribute_id')->index();
            $table->tinyInteger('status')->default(1);

            $table->timestamps();
        });

        Schema::table('attributes', function (Blueprint $table) {
            $table->foreign('group_attribute_id')->references('id')->on('group_attributes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attributes');
    }
}
