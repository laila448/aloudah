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
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('mother_name');
            $table->string('gender');
            $table->date('date_of_birth');
            $table->string('address');
            $table->integer('national_number')->unique();
            $table->integer('vacations');
            $table->integer('salary');
            $table->integer('rewards');
            $table->date('employment_date');
            $table->date('resignation_date');
            $table->string('manager_name');
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
        Schema::dropIfExists('drivers');
    }
};
