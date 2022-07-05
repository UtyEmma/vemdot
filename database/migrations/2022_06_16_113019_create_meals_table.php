<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMealsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meals', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id');
            $table->string('user_id');
            $table->string('category');
            $table->string('name');
            $table->string('thumbnail');
            $table->longText('description');
            $table->string('price');
            $table->longText('images');
            $table->string('video');
            $table->string('discount');
            $table->string('tax');
            $table->string('rating');
            $table->string('availability');
            $table->string('promoted');
            $table->softDeletes();
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
        Schema::dropIfExists('meals');
    }
}
