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
        Schema::create('trips', function (Blueprint $table) {
                    $table->id();
                    $table->unsignedBigInteger('truck_id');
                    $table->foreign('truck_id')->references('id')->on('trucks')->onDelete('cascade');
                    $table->unsignedBigInteger('driver_id');
                    $table->foreign('driver_id')->references('id')->on('drivers')->onDelete('cascade');
                    $table->unsignedBigInteger('employee_id');
                    $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
                    $table->unsignedBigInteger('branch_id');
                    $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
                    // $table->unsignedBigInteger('manifest_id');
                    // $table->foreign('manifest_id')->references('id')->on('manifests')->onDelete('cascade');
                    $table->string('number');
                    $table->date('date');
                    $table->string('source');
                    $table->string('destination');
                    $table->integer('status');
                    $table->date('arrival_date');
                    $table->string('created_by');
                    $table->string('edited_by');
                    $table->text('notes');
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
        Schema::dropIfExists('trips');
    }
};
