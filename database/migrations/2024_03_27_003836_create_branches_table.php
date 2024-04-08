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
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
           // $table->unsignedBigInteger('branchmanager_id');
            //$table->foreign('branchmanager_id')->references('id')->on('branch_managers')->onDelete('cascade');
            $table->string('address');
            $table->integer('phone');
<<<<<<< HEAD
            $table->date('date');
            $table->softDeletes();
=======
            $table->date('opening_date');
            $table->date('closing_date')->nullable();
>>>>>>> 17f1ee999194428867a5e1001b0539690ca72eba
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
        Schema::dropIfExists('branches');
    }
};
