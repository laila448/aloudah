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
            $table->string('name')->unique();
             $table->string('email')->unique();
             $table->timestamp('email_verified_at')->nullable();
             $table->string('password');
            $table->integer('phone_number')->unique();
            $table->string('gender');    
            $table->unsignedBigInteger('warehouse_id');
             $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');
             $table->string('mother_name');
             $table->date('date_of_birth');
             $table->string('address');
             $table->integer('vacations');
             $table->integer('salary');
             $table->string('rank');
             $table->date('employment_date');
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
