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
        Schema::create('profiles', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('first_name');
            $table->string('last_name');
            $table->date('birthday')->nullable();
            $table->string('profile_photo')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->string('street_address')->nullable();
            $table->string('city')->nullable();
            $table->timestamps();
            $table->primary('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
