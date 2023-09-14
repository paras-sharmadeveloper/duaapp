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
        Schema::create('venue_addresses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('venue_id');
            $table->unsignedBigInteger('therapist_id');
            $table->text('address');
            $table->date('venue_date'); 
            $table->time('slot_starts_at');
            $table->time('slot_ends_at');
            $table->enum('type', ['on-site', 'virtual']);
            $table->timestamps(); 
            $table->foreign('venue_id')
                  ->references('id')
                  ->on('venues');
                //   ->onDelete('cascade');
            $table->foreign('therapist_id')
                  ->references('id')
                  ->on('users'); 
                //   ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('venue_addresses');
    }
};
