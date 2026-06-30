<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->foreignId('dining_session_id')->nullable()->constrained('dining_sessions')->onDelete('set null');

            // Table & area placement
            $table->foreignId('table_id')->nullable()->constrained('tables')->onDelete('set null');
            $table->foreignId('original_table_id')->nullable()->constrained('tables')->onDelete('set null'); // first table before any transfer
            $table->foreignId('dining_area_id')->nullable()->constrained('dining_areas')->onDelete('set null');

            $table->string('order_number')->index();
            $table->string('source')->default('qr'); // qr, waiter, pos, phone, kiosk

            // Staff assignments
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('waiter_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('chef_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('cashier_id')->nullable()->constrained('users')->onDelete('set null');

            // Pricing
            $table->decimal('subtotal', 10, 2)->default(0.00);
            $table->decimal('discount_amount', 10, 2)->default(0.00);
            $table->decimal('tax_amount', 10, 2)->default(0.00);
            $table->decimal('service_charge', 10, 2)->default(0.00);
            $table->decimal('grand_total', 10, 2)->default(0.00);

            // Status
            $table->string('current_status', 40)->default('pending_waiter_approval');
            // values: pending_waiter_approval, approved, sent_to_kitchen, in_preparation,
            //         ready, picked_by_waiter, served, bill_requested, paid, closed, cancelled, rejected
            $table->string('payment_status', 20)->default('unpaid');
            // values: unpaid, partial, paid, refunded, voided

            // Full timestamp chain (SRS Section 7)
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('sent_to_kitchen_at')->nullable();
            $table->timestamp('chef_started_at')->nullable();
            $table->timestamp('chef_completed_at')->nullable();
            $table->timestamp('waiter_picked_at')->nullable(); // KEY: blocks self-service table transfer
            $table->timestamp('served_at')->nullable();
            $table->timestamp('bill_requested_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            // Table transfer tracking
            $table->unsignedInteger('table_transfer_count')->default(0); // how many times the table was changed
            $table->timestamp('last_table_transfer_at')->nullable();
            $table->timestamp('waiter_alerted_at')->nullable(); // when customer hit "Alert My Waiter" button

            // Cancellation / rejection reasons
            $table->text('cancellation_reason')->nullable();
            $table->text('rejection_reason')->nullable();

            $table->timestamps();
            $table->softDeletes(); // deleted_at

            // Indexes
            $table->index('dining_session_id', 'idx_orders_dining_session');
            $table->index('current_status', 'idx_orders_status');
            $table->index('payment_status', 'idx_orders_payment_status');
            $table->index('created_at',     'idx_orders_created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
