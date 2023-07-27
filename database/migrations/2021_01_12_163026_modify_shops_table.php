<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyShopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->double('balance')->default(0)->after('address');
            $table->double('system_commission')->default(10)->comment('system commission before shipping in %')->after('password');
            $table->double('minimum_order')->default(-1)->comment('minimum order amount, -1=no limit')->after('password');
            $table->string('email_otp')->nullable()->after('email_verified_at');
            $table->timestamp('phone_verified_at')->nullable()->after('phone');
            $table->string('sms_otp')->nullable()->after('phone');
            $table->tinyInteger('push_notification')->default(1)->comment('1=enabled, 0=disabled')->after('password');
            $table->string('device_token')->nullable()->after('remember_token');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->dropColumn('balance');
            $table->dropColumn('system_commission');
            $table->dropColumn('minimum_order');
            $table->dropColumn('email_otp');
            $table->dropColumn('phone_verified_at');
            $table->dropColumn('sms_otp');
            $table->dropColumn('push_notification');
            $table->dropColumn('device_token');
        });
    }
}
