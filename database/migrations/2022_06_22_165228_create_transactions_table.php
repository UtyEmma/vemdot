<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id')->unique();
            $table->string('user_id');
            $table->string('type');
            $table->decimal('amount', 13,2)->default(0);
            $table->string('payment_method')->nullable();
            $table->string('channel')->nullable();
            $table->string('reference')->nullable();
            $table->string('access_code')->nullable();
            $table->string('orderID')->nullable();
            $table->text('description')->nullable();
            $table->string('status')->default('pending');

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
        Schema::dropIfExists('transactions');
    }
}
