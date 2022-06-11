<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id')->unique();
            $table->string('email')->unique();
            $table->string('firstname');
            $table->string('lastname');
            $table->string('phone');

            $table->string('account_type')->default('user');
            $table->string('status')->default('active');
            $table->string('country')->nullable();
            $table->string('gender')->nullable();
            $table->text('address')->nullable();
            $table->decimal('main_balance', 13,2)->default(0);
            $table->decimal('ref_balance', 13,2)->default(0);

            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            $table->string('avatar')->default('default.png');
            $table->string('referral_id');
            $table->string('referred_id')->nullable();

            $table->string('first_time_login')->default('yes');

            $table->softDeletes();  //add this line
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
