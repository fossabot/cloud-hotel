<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoomTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('room_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('_id')->unique()->index();
            $table->string('name')->unique()->index();
            $table->timestamps();
            $table->softDeletes();
        });
        
        Schema::create('room_type_rooms', function (Blueprint $table) {
            $table->unsignedBigInteger('room_id')->index();
            $table->unsignedBigInteger('room_type_id')->index();
            $table->unsignedBigInteger('hotel_id')->index();
        });
        
        Schema::create('room_type_hotels', function (Blueprint $table) {
            $table->unsignedBigInteger('hotel_id')->index();
            $table->unsignedBigInteger('room_type_id')->index();
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('room_type_hotels');
        Schema::dropIfExists('room_type_rooms');
        Schema::dropIfExists('room_types');
    }
}
