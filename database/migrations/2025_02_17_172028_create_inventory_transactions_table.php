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
        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('staff_id')->nullable()->constrained()->onDelete('set null'); // auth
            $table->enum('type', ['purchase', 'use', 'adjustment','return']);
            $table->integer('quantity');
            $table->decimal('unit_price');
            $table->date('transaction_date');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_transactions');
    }
};
