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
        Schema::create('goods', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('warehouse_id');
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');
            $table->string('type');
            $table->integer('quantity')->default(1);
            $table->double('weight');
            $table->string('size');
            $table->string('content');
            $table->string('marks');
           // $table->integer('price');
            $table->string('truck');
            $table->string('driver');
            //$table->string('desk');
            $table->string('destination');
            $table->date('ship_date');
            $table->date('date');
            $table->string('sender');
            $table->string('receiver');
            $table->string('barcode');
            $table->boolean('received')->default(0);
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
        Schema::dropIfExists('goods');
    }
};
