<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id');
            $table->string('user_id');
            $table->string('vendor_id')->nullable();
            $table->string('courier_id')->nullable();
            $table->string('bike_id')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('receiver_id')->nullable();
            $table->string('delivery_distance')->nullable();
            $table->longText('instructions')->nullable();
            $table->string('amount');
            $table->json('meals');
            $table->string('delivery_fee');
            $table->string('delivery_method');
            $table->string('receiver_name');
            $table->string('receiver_phone')->nullable();
            $table->string('receiver_location');
            $table->string('status');
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
        Schema::dropIfExists('orders');
    }
}
