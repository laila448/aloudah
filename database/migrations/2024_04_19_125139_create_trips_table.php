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
            $table->unsignedBigInteger('branch_id');
<<<<<<< HEAD
           
             $table->foreign('manifest_id')->references('id')->on('manifests')->onDelete('cascade');
=======
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->unsignedBigInteger('manifest_id')->nullable(); // Make manifest_id nullable
            $table->foreign('manifest_id')->references('id')->on('manifests')->onDelete('cascade');
>>>>>>> b8d37973ef91fa1b55801f44f3c24c3fdf7e92f1
            $table->string('number')->unique();
            $table->date('date');
            $table->string('status')->default('active');
            $table->date('arrival_date')->nullable();
            $table->string('created_by');
            $table->string('edited_by')->nullable();
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
