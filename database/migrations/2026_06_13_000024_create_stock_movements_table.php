<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_item_id')->constrained('inventory_items')->onDelete('cascade');
            $table->string('type'); // purchase, deduction, adjustment, wastage, return, transfer
            $table->decimal('quantity_in', 12, 4)->default(0.0000);
            $table->decimal('quantity_out', 12, 4)->default(0.0000);
            $table->string('reference_type')->nullable(); // Order, Purchase, Wastage, Adjustment
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->decimal('unit_cost', 10, 2)->default(0.00);
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('created_at')->nullable();

            $table->index(['inventory_item_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
