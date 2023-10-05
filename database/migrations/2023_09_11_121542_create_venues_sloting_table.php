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
        Schema::create('venues_sloting', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('venue_address_id'); // Foreign key to venue_addresses
            $table->time('slot_time');
            $table->timestamps();
            $table->foreign('venue_address_id')
                  ->references('id')
                  ->on('venue_addresses')->onDelete('no action');
                  
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('venues_sloting');
    }
};
