<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWithdrawsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('withdraws', function (Blueprint $table) {
            $table->id();
            $table->morphs('user');
            $table->unsignedBigInteger('withdraw_method_id');
            $table->double('amount')->default(0);
            $table->double('charge')->default(0);
            $table->longText('fields')->nullable()->comment('json encoded title => value');
            $table->tinyInteger('status')->default(0)->comment('0=pending, 1=approved, 2=refunded');
            $table->timestamps();
            $table->foreign('withdraw_method_id')->references('id')->on('withdraw_methods')->onUpdate('CASCADE')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('withdraws');
    }
}
