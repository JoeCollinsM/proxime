<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBalanceColumnToUsersAndDeliveryMenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->double('balance')->default(0)->comment('wallet balance in default currency')->after('phone');
        });
        Schema::table('delivery_men', function (Blueprint $table) {
            $table->double('balance')->default(0)->comment('wallet balance in default currency')->after('phone');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('balance');
        });
        Schema::table('delivery_men', function (Blueprint $table) {
            $table->dropColumn('balance');
        });
    }
}
