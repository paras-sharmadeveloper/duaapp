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
            $table->string('token_id')->nullable();
            $table->enum('type',['dua','dum','working_lady_dua','working_lady_dum','special_token','none'])->default('none');
            $table->timestamps();
            $table->foreign('venue_address_id')
                  ->references('id')
                  ->on('venue_addresses')->onDelete('cascade')->onUpdate('cascade');

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
