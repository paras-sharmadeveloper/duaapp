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
        Schema::create('whats_apps', function (Blueprint $table) {
            $table->id();
            $table->string('customer_number'); 
            $table->string('customer_response'); 
            $table->json('bot_reply')->nullable();
            $table->json('data_sent_to_customer')->nullable();
            $table->datetime('last_reply_time')->nullable(); 
            $table->tinyInteger('steps')->default(0)->comment('customer_init => 0');
            $table->string('lang')->nullable(); 
            $table->string('dua_option')->nullable(); 
            $table->string('response_options')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whats_apps');
    }
};
