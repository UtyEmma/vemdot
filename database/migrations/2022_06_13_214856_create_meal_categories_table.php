<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMealCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meal_categories', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id')->unique();
            
            $table->string('name');
            $table->string('status')->default('pending');
            $table->text('description')->nullable();
            $table->string('thumbnail')->nullable();
            
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
        Schema::dropIfExists('meal_categories');
    }
}
