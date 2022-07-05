<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSiteSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id')->unique();
            $table->string('site_name')->nullable();
            $table->string('site_email')->nullable();
            $table->string('site_phone')->nullable();
            $table->string('site_address')->nullable();
            $table->string('site_domain')->nullable();
            $table->string('site_logo')->nullable();
            $table->string('referral_bonus')->default(5);
            $table->string('account_verification')->default('no');
            $table->string('token_length')->default(4);
            $table->string('login_alert')->default('no');
            $table->string('welcome_message')->default('no');
            $table->string('send_basic_emails')->default('no');
            $table->string('automatic_withdraw')->default('no');

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
        Schema::dropIfExists('site_settings');
    }
}
