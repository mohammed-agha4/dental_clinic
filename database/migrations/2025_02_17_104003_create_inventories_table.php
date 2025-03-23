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
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constraind()->onDelete('set null');
            $table->foreignId('supplier_id')->nullable()->constraind()->onDelete('set null');
            $table->string('name');
            $table->string('SKU')->unique();
            $table->string('description')->nullable();
            $table->integer('quantity')->default(0);
            $table->integer('reorder_level')->default(10);
            $table->decimal('unit_price',8,2);
            $table->date('expiry_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
