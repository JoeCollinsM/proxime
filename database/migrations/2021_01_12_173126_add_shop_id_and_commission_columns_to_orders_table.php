<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShopIdAndCommissionColumnsToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('shop_id')->nullable()->after('user_id');
            $table->double('shop_commission')->default(0)->comment('shop commission in default currency')->after('shipping_charge');
            $table->double('system_commission')->default(0)->comment('system commission in default currency')->after('shipping_charge');
            $table->foreign('shop_id')->references('id')->on('shops')->cascadeOnUpdate()->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['shop_id']);
            $table->dropColumn('shop_id');
            $table->dropColumn('shop_commission');
            $table->dropColumn('system_commission');
        });
    }
}
