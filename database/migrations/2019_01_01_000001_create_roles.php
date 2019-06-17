<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('_id')->unique()->index();
            $table->string('name')->unique()->index();
            $table->json('permissions')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
        
        Schema::create('role_users', function (Blueprint $table) {
            $table->unsignedBigInteger("role_id")->index();
            $table->unsignedBigInteger("user_id")->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('role_users');
        Schema::dropIfExists('roles');
    }
}
