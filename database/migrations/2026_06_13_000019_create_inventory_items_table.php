<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->string('name');
            $table->string('category')->nullable();
            $table->string('unit'); // kg, g, l, ml, pcs, pack
            $table->decimal('current_stock', 12, 4)->default(0.0000);
            $table->decimal('min_stock', 12, 4)->default(0.0000);
            $table->decimal('reorder_level', 12, 4)->default(0.0000);
            $table->decimal('average_cost', 10, 2)->default(0.00);
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_items');
    }
};
