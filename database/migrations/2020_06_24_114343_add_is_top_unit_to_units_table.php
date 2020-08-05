<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsTopUnitToUnitsTable extends Migration{
    /**
     * Run the migrations.
     * @return void
     */
    public function up(){

        Schema::table('units', function(Blueprint $table){
            $table->boolean("is_top")->default(0)->after("postal_code");
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down(){
        Schema::table('units', function(Blueprint $table){
            //
        });
    }
}
