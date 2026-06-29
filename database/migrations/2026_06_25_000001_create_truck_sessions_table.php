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
        Schema::create('truck_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('truck_id')->constrained('trucks')->onDelete('cascade');
            $table->string('destination');
            $table->string('purpose')->nullable();
            $table->dateTime('checkout_time');
            $table->dateTime('checkin_time')->nullable();
            $table->foreignId('checkout_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('checkin_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('truck_sessions');
    }
};
