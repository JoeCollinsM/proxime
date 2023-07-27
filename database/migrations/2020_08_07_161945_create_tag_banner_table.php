<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTagBannerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tag_banner', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('banner_id');
            $table->unsignedBigInteger('tag_id');
            $table->timestamps();
            $table->foreign('banner_id')->references('id')->on('banners')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('tag_id')->references('id')->on('tags')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tag_banner');
    }
}
