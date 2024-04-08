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
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            // $table->unsignedBigInteger('branch_id');
            // $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
           // $table->unsignedBigInteger('wmanager_id');
            //$table->foreign('wmanager_id')->references('id')->on('warehouse_managers')->onDelete('cascade');
           $table->string('address');
           $table->string('branch');
           $table->string('area');
           $table->string('notes');
           $table->softDeletes();
         //  $table->integer('phone_number');
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
        Schema::dropIfExists('warehouses');
    }
};
