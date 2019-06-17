<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRooms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('_id')->unique()->index();
            $table->string('name')->unique()->index();
            $table->timestamps();
            $table->softDeletes();
        });
        
        Schema::create('room_hotels', function (Blueprint $table) {
            $table->unsignedBigInteger('hotel_id')->index();
            $table->unsignedBigInteger('room_id')->index();
        });
        
        Schema::create('room_images', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('_id')->unique()->index();
            $table->unsignedBigInteger('hotel_id')->index();
            $table->unsignedBigInteger('room_id')->index();
            $table->string('image')->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('room_images');
        Schema::dropIfExists('room_hotels');
        Schema::dropIfExists('rooms');
    }
}
