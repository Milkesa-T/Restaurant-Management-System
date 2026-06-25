<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_performance', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('order_id')->unique()->constrained('orders')->onDelete('cascade');

            $table->integer('approval_duration_sec')->nullable();
            $table->integer('kitchen_queue_wait_sec')->nullable();
            $table->integer('chef_prep_duration_sec')->nullable();
            $table->integer('pickup_delay_sec')->nullable();
            $table->integer('serving_duration_sec')->nullable();
            $table->integer('payment_duration_sec')->nullable();
            $table->integer('total_lifecycle_sec')->nullable();

            $table->boolean('approval_sla_breached')->default(false);
            $table->boolean('prep_sla_breached')->default(false);
            $table->boolean('pickup_sla_breached')->default(false);
            $table->boolean('service_sla_breached')->default(false);
            $table->boolean('overall_sla_breached')->default(false);

            $table->timestamp('last_calculated_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_performance');
    }
};
