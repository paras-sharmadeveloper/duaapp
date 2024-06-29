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
        Schema::create('visitor_temps', function (Blueprint $table) {
            $table->id();
            $table->json('user_inputs')->nullable();
            $table->string('message')->nullable();
            $table->string('msg_sid')->nullable();
            $table->string('msg_sent_status')->nullable();
            $table->string('msg_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitor_temps');
    }
};
