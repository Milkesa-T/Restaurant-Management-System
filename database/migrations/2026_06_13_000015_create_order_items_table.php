<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('menu_item_id')->constrained('menu_items')->onDelete('cascade');
            $table->foreignId('variant_id')->nullable()->constrained('item_variants')->onDelete('set null');
            $table->integer('quantity');
            $table->text('notes')->nullable();
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->string('current_status')->default('pending_waiter_approval');
            $table->foreignId('chef_id')->nullable()->constrained('users')->onDelete('set null');
            
            // KDS pacing & timer tracking columns
            $table->timestamp('scheduled_fire_at')->nullable();
            $table->timestamp('cooking_started_at')->nullable();
            $table->timestamp('cooking_completed_at')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
