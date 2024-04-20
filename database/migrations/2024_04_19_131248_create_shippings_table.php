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
            $table->unsignedBigInteger('manifest_id');
            
            $table->foreign('manifest_id')->references('id')->on('manifests')->onDelete('cascade');
            $table->unsignedBigInteger('customer_id');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->string('sender');
            $table->string('receiver');
            // $table->date('date');
            $table->string('number');
            $table->string('source');
            // $table->string('destination');
            $table->integer('quantity');
            $table->string('type');
            $table->double('weight');
            $table->string('size');
            $table->string('content');
            $table->string('marks');
            $table->string('notes');
            $table->string('shipping_cost');
            $table->string('against_shipping');
            $table->string('adapter');
            $table->string('advance');
            $table->string('miscellaneous');
            $table->string('prepaid');
            $table->string('discount');
            $table->string('collection');
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
