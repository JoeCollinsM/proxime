<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('payment_method_id')->nullable();
            $table->string('payment_method_name')->nullable();
            $table->string('track')->unique()->comment('payment tracking number');
            $table->double('net_amount')->default(0);
            $table->double('charge')->default(0)->comment('gateway charge');
            $table->double('gross_amount')->default(0);
            $table->string('transaction_id')->nullable();
            $table->longText('params')->nullable();
            $table->text('note')->nullable();
            $table->tinyInteger('status')->default(0)->comment('0=pending, 1=completed, 2=hold, 3=canceled');
            $table->timestamps();
            $table->foreign('order_id')->references('id')->on('orders')->onUpdate('CASCADE')->onDelete('RESTRICT');
            $table->foreign('payment_method_id')->references('id')->on('payment_methods')->onUpdate('CASCADE')->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
}
