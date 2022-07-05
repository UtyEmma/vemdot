<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAutomaticWithdrawColumnToSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasColumn('site_settings', 'automatic_withdraw')){
            Schema::table('site_settings', function (Blueprint $table) {
                //
                $table->string('automatic_withdraw')->default('no')->after('send_basic_emails');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('site_settings', function (Blueprint $table) {
            //
        });
    }
}
