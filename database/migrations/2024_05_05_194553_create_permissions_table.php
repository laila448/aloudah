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
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->boolean('add_trip')->default(0);
            $table->boolean('edit_trip')->default(0);
            $table->boolean('delete_trip')->default(0);
            $table->boolean('drawer')->default(0);
            $table->boolean('email')->default(0);
            $table->boolean('trip_list')->default(0);
            $table->boolean('print_road')->default(0);
            $table->boolean('print_trips')->default(0);
            $table->boolean('edit_close')->default(0);
            $table->boolean('add_manifest')->default(0);
            $table->boolean('edit_manifest')->default(0);
            $table->boolean('delete_manifest')->default(0);
            $table->boolean('view_manifest')->default(0);
            $table->boolean('add_report')->default(0);
            $table->boolean('edit_report')->default(0);
            $table->boolean('delete_report')->default(0);
            $table->boolean('view_report')->default(0);
            $table->boolean('add_misc')->default(0);
            $table->boolean('edit_misc')->default(0);
            $table->boolean('delete_misc')->default(0);
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
        Schema::dropIfExists('permissions');
    }
};
