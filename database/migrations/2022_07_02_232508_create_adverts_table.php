<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdvertsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    { // 
        Schema::create('adverts', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id')->unique();
            $table->string('user_id');
            
            $table->string('email');
            $table->string('caption');
            $table->string('banner');
            $table->text('description')->nullable();
            $table->string('status');

            $table->softDeletes();  //add this line
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
        Schema::dropIfExists('adverts');
    }
}
