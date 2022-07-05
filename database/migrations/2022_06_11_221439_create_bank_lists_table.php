<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBankListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bank_lists', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id')->unique();
            $table->string('name')->nullable();
            $table->string('slug')->nullable();
            $table->string('code')->nullable();
            $table->string('longcode')->nullable();
            $table->string('country')->nullable();
            $table->string('currency')->nullable();
            $table->string('status')->default('active');
            $table->string('logo')->nullable();

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
        Schema::dropIfExists('bank_lists');
    }
}
