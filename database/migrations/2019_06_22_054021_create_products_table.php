<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id('id');

            $table->unsignedBigInteger('parent_id')->nullable()->comment('null for simple product');
            $table->unsignedBigInteger('category_id')->nullable()->comment('null for variant');
            $table->unsignedBigInteger('shop_id')->nullable()->comment('null for variant');

            $table->string('title');
            $table->string('slug')->unique()->nullable()->comment('null for variant');
            $table->text('excerpt')->nullable();
            $table->longText('content')->nullable();
            $table->string('image')->nullable();

            $table->bigInteger('views')->default(0);

            $table->double('per')->default(1);
            $table->string('unit')->default('kg');
            // Start: Those column will be used for variation
            $table->double('sale_price')->default(0);
            $table->double('general_price')->default(0);
            $table->string('sku')->nullable();
            $table->double('stock')->default(-1)->comment('-1 for unlimited');
            // End: Those column will be used for variation
            $table->tinyInteger('delivery_time')->default(1);
            $table->tinyInteger('delivery_time_type')->default(2)->comment('1=hour, 2=day, 3=week, 4=month');

            $table->tinyInteger('status')->default(0)->comment('0=pending, 1=activated, 2=deactivated');

            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('products')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('RESTRICT');
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
