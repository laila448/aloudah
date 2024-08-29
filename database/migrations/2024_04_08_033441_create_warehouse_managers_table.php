<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('warehouse_managers', function (Blueprint $table) {
            $table->id();  
            $table->string('national_id')->unique()->nullable();;
            $table->string('name')->unique();
             $table->string('email')->unique();
             $table->timestamp('email_verified_at')->nullable();
             $table->string('password');
            $table->integer('phone_number')->unique();
            $table->string('gender')->nullable();;    
            $table->unsignedBigInteger('warehouse_id');
             $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');
             $table->string('mother_name')->nullable();;
             $table->date('date_of_birth')->nullable();;
             $table->string('manager_address');
             $table->integer('salary')->nullable();;
             $table->string('rank')->default('warehouse_manager');
             $table->date('employment_date')->nullable();;
             $table->text('device_token')->nullable();
             $table->softDeletes();
             $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('warehouse_managers');
    }
};
