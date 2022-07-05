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
            $table->string('name');
            $table->string('phone');

            $table->string('role');
            $table->string('status');
            $table->string('country')->nullable();
            $table->string('gender')->nullable();

            $table->timestamp('email_verified_at')->nullable();
            $table->string('two_factor');
            $table->timestamp('two_factor_verified_at')->nullable();
            $table->string('two_factor_access');
            $table->string('password');

            $table->string('avatar')->nullable();
            $table->string('referral_id');
            $table->string('referred_id')->nullable();

            $table->decimal('main_balance', 13,2);
            $table->decimal('ref_balance', 13,2);

            $table->string('first_time_login');

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
