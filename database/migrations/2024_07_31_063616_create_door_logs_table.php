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
        Schema::create('door_logs', function (Blueprint $table) {
            $table->id();
            $table->string('SN')->nullable();
            $table->string('SCode')->nullable();
            $table->string('DeviceID')->nullable();
            $table->string('ReaderNo')->nullable();
            $table->string('ActIndex')->nullable();


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('door_logs');
    }
};
