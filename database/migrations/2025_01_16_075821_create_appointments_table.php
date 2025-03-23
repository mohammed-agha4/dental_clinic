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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('staff_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('service_id')->constrained()->onDelete('restrict');
            $table->dateTime('appointment_date');
            $table->integer('duration')->default(30); // in minutes
            $table->enum('status', ['scheduled', 'rescheduled', 'completed', 'canceled', 'walk_in']);
            $table->text('notes')->nullable();
            $table->string('cancellation_reason')->nullable();
            $table->boolean('reminder_sent')->default(false);
            $table->softDeletes();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
