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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('national_id');
            $table->string('name')->unique();
             $table->string('email')->unique();
             $table->timestamp('email_verified_at')->nullable();
             $table->string('password');
            $table->string('phone_number')->unique();
             $table->unsignedBigInteger('branch_id');
             $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->string('mother_name');
            $table->string('gender');
            $table->date('birth_date');
            $table->string('birth_place');
            $table->string('mobile');
            $table->string('address');
          //  $table->integer('national_number')->unique();
          //  $table->integer('vacations');
            $table->integer('salary');
            $table->string('rank');
          //  $table->integer('rewards');
            $table->date('employment_date');
            $table->date('resignation_date')->nullable();
            $table->string('manager_name');
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
        Schema::dropIfExists('employees');
    }
};
