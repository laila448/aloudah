<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('warehouses', function (Blueprint $table) {
            $table->unsignedBigInteger('warehouse_manager_id')->nullable()->after('id');
            $table->foreign('warehouse_manager_id')->references('id')->on('warehouse_managers')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('warehouses', function (Blueprint $table) {
            $table->dropForeign(['warehouse_manager_id']);
            $table->dropColumn('warehouse_manager_id');
        });
    }
};
