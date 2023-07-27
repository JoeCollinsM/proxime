<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('code');
            $table->timestamp('start_at')->nullable();
            $table->timestamp('expire_at')->nullable();
            $table->double('min')->default(-1)->comment('Minimum Cart Amount, -1=Any Amount');
            $table->double('upto')->default(-1)->comment('Upto Discount Amount (Only for percent amount), -1=not needed');
            $table->bigInteger('maximum_use_limit')->default(-1);
            $table->tinyInteger('discount_type')->default(1)->comment('1=percent, 2=raw amount');
            $table->double('amount')->default(0);

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
        Schema::dropIfExists('coupons');
    }
}
