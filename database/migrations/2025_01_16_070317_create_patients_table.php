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
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('fname');
            $table->string('lname');
            $table->date('DOB');
            $table->enum('gender', ['male', 'female']);
            $table->string('phone')->unique();
            $table->string('email')->unique();
            $table->text('medical_history')->nullable();
            $table->text('allergies')->nullable();
            $table->string('Emergency_contact_name')->nullable();
            $table->string('Emergency_contact_phone')->nullable();
            $table->date('last_visit_date')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
