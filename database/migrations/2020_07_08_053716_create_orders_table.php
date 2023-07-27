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
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('track')->unique()->comment('order tracking number');

            $table->unsignedBigInteger('coupon_id')->nullable();
            $table->string('coupon_code')->nullable();
            $table->double('discount')->default(0);

            $table->unsignedBigInteger('shipping_method_id')->nullable();
            $table->string('shipping_method_name')->nullable();
            $table->double('shipping_charge')->default(0);

            $table->tinyInteger('status')->default(-1)->comment('-1=no entry, 0=pending, 1=processing, 2=on the way, 3=delivered, 4=hold, 5=canceled');
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
            $table->foreign('coupon_id')->references('id')->on('coupons')->onUpdate('CASCADE')->onDelete('SET NULL');
            $table->foreign('shipping_method_id')->references('id')->on('shipping_methods')->onUpdate('CASCADE')->onDelete('SET NULL');
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
