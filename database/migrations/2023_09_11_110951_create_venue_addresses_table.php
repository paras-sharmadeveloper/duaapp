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
            $table->unsignedBigInteger('siteadmin_id');
            $table->unsignedBigInteger('combination_id')->nullable();

            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->text('address');
            $table->text('address_ur')->nullable();
            $table->dateTime('venue_date');
            $table->dateTime('venue_date_end');
            // $table->time('slot_starts_at_morning')->nullable();
            // $table->time('slot_ends_at_morning')->nullable();

            // $table->time('slot_starts_at_evening')->nullable();
            // $table->time('slot_ends_at_evening')->nullable();

            $table->enum('type', ['on-site', 'virtual']);
            $table->string('room_name')->nullable();
            $table->string('room_sid')->nullable();
            $table->integer('slot_duration')->default(1);
            $table->integer('slot_appear_hours')->default(24);
            $table->integer('is_monday')->default(0);
            $table->integer('is_tuesday')->default(0);
            $table->integer('is_wednesday')->default(0);
            $table->integer('is_thursday')->default(0);
            $table->integer('is_friday')->default(0);
            $table->integer('is_saturday')->default(0);
            $table->integer('is_sunday')->default(0);
            $table->integer('recurring_till')->default(0);
            $table->integer('rejoin_venue_after')->default(0);
            $table->integer('selfie_verification')->default(1);


            $table->integer('dua_slots')->default(0);
            $table->integer('dum_slots')->default(0);

            $table->integer('working_lady_dua')->nullable();
            $table->integer('working_lady_dum')->nullable();
            $table->integer('repeat_visitor_days')->default(0);

            

            $table->text('status_page_note')->nullable();

            $table->text('status_page_note_ur')->nullable();

            $table->string('timezone')->nullable();

            $table->json('venue_available_country')->nullable();
            $table->timestamps();
            $table->foreign('combination_id')
                  ->references('id')
                  ->on('venue_state_cities')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('venue_id')
                  ->references('id')
                  ->on('venues')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('therapist_id')
                  ->references('id')
                  ->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('siteadmin_id')
                ->references('id')
                ->on('users')->onDelete('cascade')->onUpdate('cascade');

                $table->integer('reject_dua_id')->nullable();
                $table->integer('reject_dum_id')->nullable();

                $table->enum('status', ['active', 'inactive'])->default('active');
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
