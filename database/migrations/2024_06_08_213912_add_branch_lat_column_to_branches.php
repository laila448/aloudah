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
        Schema::table('branches', function (Blueprint $table) {
            if (!Schema::hasColumn('branches', 'branch_lat')) {
                $table->decimal('branch_lat', 8, 2)->nullable();
            }
            if (!Schema::hasColumn('branches', 'branch_lng')) {
                $table->decimal('branch_lng', 8, 2)->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('branches', function (Blueprint $table) {
            if (Schema::hasColumn('branches', 'branch_lat')) {
                $table->dropColumn('branch_lat');
            }
            if (Schema::hasColumn('branches', 'branch_lng')) {
                $table->dropColumn('branch_lng');
            }
        });
    }
};
