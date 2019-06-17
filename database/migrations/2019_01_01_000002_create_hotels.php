<?php
    
    use Illuminate\Support\Facades\Schema;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Database\Migrations\Migration;
    
    class CreateHotels extends Migration
    {
        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create('hotels', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->uuid('_id')->unique()->index();
                $table->string('name')->unique()->index();
                $table->string('slug')->unique()->index();
                $table->string('email')->unique()->index();
                $table->string('phone')->unique()->index();
                $table->json('metas')->nullable();
                $table->boolean('status')->default(true)->index();
                $table->timestamps();
                $table->softDeletes();
            });
            
            Schema::create('hotel_users', function (Blueprint $table) {
                $table->unsignedBigInteger('hotel_id')->index();
                $table->unsignedBigInteger('user_id')->index();
            });
            
        }
        
        /**
         * Reverse the migrations.
         *
         * @return void
         */
        public function down()
        {
            Schema::dropIfExists('hotel_users');
            Schema::dropIfExists('hotels');
        }
    }
