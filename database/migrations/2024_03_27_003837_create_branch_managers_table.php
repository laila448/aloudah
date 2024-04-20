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
        Schema::create('branch_managers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
             $table->string('email')->unique();
             $table->timestamp('email_verified_at')->nullable();
             $table->string('password');
             $table->integer('phone_number')->unique();
             $table->unsignedBigInteger('branch_id');
             $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
             $table->string('gender');
             $table->string('mother_name');
             $table->date('date_of_birth');
             $table->string('manager_address');
             $table->integer('salary');
             $table->string('rank')->nullable();
             $table->date('employment_date');
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
        Schema::dropIfExists('branch_managers');
    }
};
