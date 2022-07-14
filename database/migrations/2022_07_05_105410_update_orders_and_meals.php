<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateOrdersAndMeals extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table('meals', function(Blueprint $table){
            $table->string('avg_time')->after('discount');
        });

        Schema::table('orders', function(Blueprint $table){
            $table->integer('avg_time');
            $table->string('receiver_email');
            $table->string('reference');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        //
    }
}
