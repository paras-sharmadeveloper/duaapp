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
        Schema::create('vistors', function (Blueprint $table) {
            $table->id();
            $table->string('booking_number');
            $table->string('fname');
            $table->string('lname');
            $table->string('email')->unique();
            $table->string('country_code',8)->nullable();
            $table->string('phone',15)->unique();
            $table->enum('is_whatsapp',['yes','no'])->default('no');
            $table->string('user_ip');
            $table->string('user_question')->nullable();
            $table->string('booking_uniqueid')->unique();
            $table->unsignedBigInteger('slot_id');
            $table->foreign('slot_id')
            ->references('id')
            ->on('venues_sloting');  
            $table->string('meeting_doneAt',60)->nullable(); 
            $table->text('recognized_code')->nullable(); 
            $table->timestamp('sms_sent_at')->nullable(); 
            $table->timestamp('email_sent_at')->nullable(); 
            $table->enum('is_available',['not_confirmed','confirmed'])->default('not_confirmed'); 
            $table->string('confirmed_at',60)->nullable(); 
            
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vistors');
    }
};
