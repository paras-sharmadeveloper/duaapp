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
        Schema::create('whatsapp_notification_logs', function (Blueprint $table) {
            $table->id();
            $table->string('venue_date')->nullable();
            $table->string('dua_type')->nullable();
            $table->string('whatsAppMessage')->nullable();
            $table->string('mobile')->nullable();
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
        Schema::dropIfExists('whatsapp_notification_logs');
    }
};
