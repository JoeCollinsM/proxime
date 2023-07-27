<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentMethodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->double('min')->default(-1)->comment('-1=no limit');
            $table->double('max')->default(-1)->comment('-1=no limit');
            $table->string('cred1')->nullable();
            $table->string('cred2')->nullable();
            $table->double('percent_charge')->default(0);
            $table->double('fixed_charge')->default(0);
            $table->tinyInteger('status')->default(1)->comment('0=disabled, 1=enabled');
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
        Schema::dropIfExists('payment_methods');
    }
}
