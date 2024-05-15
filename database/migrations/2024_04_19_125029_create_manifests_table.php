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
        Schema::create('manifests', function (Blueprint $table) {
            $table->id();
            // $table->unsignedBigInteger('trip_id');
            // $table->foreign('trip_id')->references('id')->on('trips')->onDelete('cascade');
            $table->string('number');
            $table->string('status')->default('open');
            $table->string('general_total')->nullable();
            $table->double('discount')->nullable();
            $table->string('net_total')->nullable();
            $table->string('misc_paid')->nullable();
            $table->string('against_shipping')->nullable();
            $table->string('adapter')->nullable();
            $table->string('advance')->nullable();
            $table->string('collection')->nullable();
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
        Schema::dropIfExists('manifests');
    }
};
