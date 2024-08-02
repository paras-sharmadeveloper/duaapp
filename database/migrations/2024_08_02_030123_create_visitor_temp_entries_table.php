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
        Schema::create('visitor_temp_entries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_number')->nullable();
            $table->unsignedBigInteger('venueId')->nullable();
            $table->string('country_code',8)->nullable();
            $table->string('phone',15)->nullable();
            $table->enum('is_whatsapp',['yes','no'])->default('no');
            $table->string('user_ip')->nullable();
            $table->text('recognized_code')->nullable();
            $table->string('user_timezone',155)->nullable();
            $table->string('source',25)->nullable();
            $table->string('dua_type',25)->nullable();
            $table->string('qr_code_image',2500)->nullable();
            $table->enum('lang',['en','ur'])->default('en');
            $table->unsignedBigInteger('working_lady_id')->default(0);
            $table->string('working_qr_id')->nullable();
            $table->text('captured_user_image')->charset('binary')->nullable();
            $table->string('message')->nullable();
            $table->string('msg_sid')->nullable();
            $table->string('msg_sent_status')->nullable();
            $table->string('msg_date')->nullable();
            $table->string('action_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitor_temp_entries');
    }
};
