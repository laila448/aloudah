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
            $table->date('opening_date');
            $table->string('created_by');
            $table->string('edited_by')->nullable();
            $table->date('editing_date')->nullable();
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
        Schema::dropIfExists('branches');
    }
};
