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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_manager_id')->nullable();
            $table->unsignedBigInteger('warehouse_manager_id')->nullable();
            $table->string('title');
            $table->text('body');
            $table->string('type');
            $table->boolean('is_read')->default(false);
            $table->json('data')->nullable();
            $table->string('status');
            $table->timestamps();

            $table->foreign('branch_manager_id')->references('id')->on('branch_managers')->onDelete('cascade');
            $table->foreign('warehouse_manager_id')->references('id')->on('warehouse_managers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notifications');
    }
};
