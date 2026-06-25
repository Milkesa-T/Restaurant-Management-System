<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('order_item_id')->nullable()->constrained('order_items')->onDelete('cascade');
            $table->string('from_status');
            $table->string('to_status');
            $table->foreignId('changed_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('changed_by_role')->nullable();
            $table->timestamp('changed_at');
            $table->integer('duration_in_previous_status_sec')->nullable();
            $table->integer('sla_threshold_sec')->nullable();
            $table->boolean('sla_breached')->default(false);
            $table->text('note')->nullable();
            $table->string('device_source')->nullable(); // customer_qr, waiter_app, kitchen_display, POS
            $table->timestamps();

            $table->index(['order_id', 'to_status']);
            $table->index('changed_at');
            $table->index(['order_id', 'changed_at'], 'idx_osl_order_changed');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_status_logs');
    }
};
