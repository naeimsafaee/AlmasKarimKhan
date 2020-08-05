<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('last_name')->nullable();
            $table->string('password');
            $table->string('mobile')->nullable();
            $table->unsignedBigInteger('image_id')->index()->nullable();
            $table->string('postal_code')->nullable();
            $table->unsignedBigInteger('default_address_id')->index()->nullable();
            $table->unsignedBigInteger('personal_code')->nullable();
            $table->string('home_number')->nullable();
            $table->unsignedBigInteger('city_id')->index()->nullable();
            $table->unsignedBigInteger('province_id')->index()->nullable();
            $table->timestamp('phone_verfied_at')->nullable();
            $table->string('email')->nullable();
            $table->string('birthday')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->tinyInteger('gender')->nullable();


            $table->timestamps();
        });
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('image_id')->references('id')->on('images')->onDelete('cascade');
            $table->foreign('default_address_id')->references('id')->on('addresses')->onDelete('cascade');
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade');
            $table->foreign('province_id')->references('id')->on('province')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
