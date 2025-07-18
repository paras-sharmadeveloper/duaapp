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
        Schema::create('ipinformations', function (Blueprint $table) {
            $table->id();
            $table->string('user_ip')->nullable();
            $table->string('countryName')->nullable();
            $table->string('regionName')->nullable();
            $table->string('city')->nullable();
            $table->string('postalCode')->nullable();
            $table->json('complete_data')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ipinformations');
    }
};
