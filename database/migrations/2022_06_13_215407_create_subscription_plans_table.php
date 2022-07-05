<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id')->unique();
            
            $table->string('name');
            $table->decimal('amount', 13,2)->default(0);
            $table->string('status')->default('pending');
            $table->string('duration')->default(30);
            $table->string('items')->default(5);
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
        Schema::dropIfExists('subscription_plans');
    }
}
