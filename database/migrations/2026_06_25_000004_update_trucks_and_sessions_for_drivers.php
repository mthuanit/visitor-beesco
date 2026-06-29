<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Drop driver_name from trucks table
        if (Schema::hasColumn('trucks', 'driver_name')) {
            Schema::table('trucks', function (Blueprint $table) {
                $table->dropColumn('driver_name');
            });
        }

        // 2. Add driver_id to truck_sessions table
        Schema::table('truck_sessions', function (Blueprint $table) {
            $table->foreignId('driver_id')->nullable()->after('truck_id')->constrained('drivers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('truck_sessions', function (Blueprint $table) {
            $table->dropForeign(['driver_id']);
            $table->dropColumn('driver_id');
        });

        Schema::table('trucks', function (Blueprint $table) {
            $table->string('driver_name')->nullable();
        });
    }
};
