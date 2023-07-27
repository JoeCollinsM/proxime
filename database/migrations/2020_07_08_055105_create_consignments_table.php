<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConsignmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('consignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('delivery_man_id');
            $table->string('track')->unique();
            $table->longText('notes')->nullable();
            $table->longText('images')->nullable()->comment('json encoded package images');
            $table->timestamp('start_on')->nullable();
            $table->timestamp('resolved_on')->nullable();
            $table->tinyInteger('status')->default(0)->comment('0=pending, 1=accepted, 2=rejected, 3=on the way, 4=shipped, 5=hold, 6=canceled');
            $table->longText('rejection_cause')->nullable();
            $table->timestamps();
            $table->foreign('order_id')->references('id')->on('orders')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('delivery_man_id')->references('id')->on('delivery_men')->onUpdate('CASCADE')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('consignments');
    }
}
