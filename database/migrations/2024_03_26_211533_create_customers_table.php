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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('national_id');
            $table->string('name');
           // $table->string('email')->unique();
           // $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->integer('phone_number')->unique();
            $table->string('gender');
            $table->integer('mobile')->unique();
            $table->string('address');
            $table->string('address_detail');
            $table->string('notes')->nullable();
            $table->string('added_by');

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
        Schema::dropIfExists('customers');
    }
};
