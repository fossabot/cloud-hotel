<?php
    
    use Illuminate\Support\Facades\Schema;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Database\Migrations\Migration;
    
    class CreateRoomCapacities extends Migration
    {
        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create('room_capacities', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->uuid('_id')->unique()->index();
                $table->string('name')->unique()->index();
                $table->timestamps();
                $table->softDeletes();
            });
            
            Schema::create('room_capacity_rooms', function (Blueprint $table) {
                $table->unsignedBigInteger('room_id')->index();
                $table->unsignedBigInteger('room_capacity_id')->index();
                $table->unsignedBigInteger('hotel_id')->index();
            });
            
            Schema::create('room_capacity_hotels', function (Blueprint $table) {
                $table->unsignedBigInteger('hotel_id')->index();
                $table->unsignedBigInteger('room_capacity_id')->index();
            });
        }
        
        /**
         * Reverse the migrations.
         *
         * @return void
         */
        public function down()
        {
            Schema::dropIfExists('room_capacity_hotels');
            Schema::dropIfExists('room_capacity_rooms');
            Schema::dropIfExists('room_capacities');
        }
    }
