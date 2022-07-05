<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToSiteSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->integer('cancellation_limit')->default(10);
            $table->string('charge_cancellations')->default('yes');
            $table->integer('cancellation_fee')->default(10);
            $table->integer('delivery_fee')->default(20);
            $table->integer('vendor_service_charge')->default(5);
            $table->integer('logistics_service_charge')->default(5);
        });
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
