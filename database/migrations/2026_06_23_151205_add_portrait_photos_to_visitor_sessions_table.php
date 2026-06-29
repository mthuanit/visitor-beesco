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
        Schema::table('visitor_sessions', function (Blueprint $table) {
            $table->string('portrait_photo')->nullable()->after('photo');
            $table->string('portrait_photo_checkout')->nullable()->after('checkout_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visitor_sessions', function (Blueprint $table) {
            $table->dropColumn(['portrait_photo', 'portrait_photo_checkout']);
        });
    }
};
