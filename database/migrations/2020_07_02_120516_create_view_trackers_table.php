<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateViewTrackersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('view_trackers', function (Blueprint $table) {
            $table->id();
            $table->string('user_ip');
            $table->string('user_browser');
            $table->string('user_platform');
            $table->tinyInteger('is_mobile');
            $table->tinyInteger('is_robot');
            $table->string('visited_date');
            $table->text('route');
            $table->text('referral_route');
            $table->unsignedBigInteger('count');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('view_trackers');
    }
}
