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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->foreignId('staff_id')->nullable()->constrained()->onDelete('set null');
            $table->text('description')->nullable();
            $table->decimal('amount', 10, 2);
            $table->enum('category', ['utilities', 'supplies', 'equipment', 'salary', 'maintenance', 'other']);
            $table->date('expense_date');
            $table->softDeletes();  // Add soft deletes
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
