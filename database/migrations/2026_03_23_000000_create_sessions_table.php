<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visitor_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('barcode');
            $table->string('name')->nullable();
            $table->string('cccd')->nullable();
            $table->string('phone')->nullable();
            $table->string('company')->nullable();
            $table->string('meet_person')->nullable();
            $table->string('vehicle')->nullable();
            $table->string('photo')->nullable();
            $table->dateTime('checkin_time');
            $table->dateTime('checkout_time')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visitor_sessions');
    }
};
