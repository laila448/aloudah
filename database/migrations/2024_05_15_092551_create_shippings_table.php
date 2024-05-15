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
        Schema::create('shippings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('source_id');
            $table->foreign('source_id')->references('id')->on('branches')->onDelete('cascade');
            $table->unsignedBigInteger('destination_id');
            $table->foreign('destination_id')->references('id')->on('branches')->onDelete('cascade');
            $table->unsignedBigInteger('manifest_id')->nullable();
            $table->foreign('manifest_id')->references('id')->on('manifests')->onDelete('cascade');
            $table->string('sender');
            $table->string('receiver');
            $table->string('sender_number');
            $table->string('receiver_number');
            $table->bigInteger('number');
            $table->unsignedBigInteger('price_id');
            $table->foreign('price_id')->references('id')->on('prices')->onDelete('cascade');
            $table->double('weight');
            $table->string('size');
            $table->string('content');
            $table->string('marks');
            $table->string('notes')->nullable();
            $table->string('shipping_cost')->nullable();
            $table->string('against_shipping')->nullable();
            $table->string('adapter')->nullable();
            $table->string('advance')->nullable();
            $table->string('miscellaneous')->nullable();
            $table->string('prepaid')->nullable();
            $table->string('discount')->nullable();
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
        Schema::dropIfExists('shippings');
    }
};
