<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWalletWithdrawalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wallet_withdrawals', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id')->unique();
            $table->string('user_id');
            $table->string('bank_id');
            $table->string('type');
            $table->decimal('amount', 13,2);
            $table->string('payment_method')->nullable();
            $table->string('recipient_code')->nullable();
            $table->string('integration')->nullable();
            $table->string('reference')->nullable();
            $table->string('transfer_code')->nullable();
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
        Schema::dropIfExists('wallet_withdrawals');
    }
}
