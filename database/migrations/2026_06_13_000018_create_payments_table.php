<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->string('method'); // cash, card, mobile_wallet, split
            $table->decimal('amount', 10, 2);
            $table->string('reference')->nullable();
            $table->foreignId('received_by_user_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('paid_at');
            $table->string('status')->default('completed'); // completed, refunded, failed
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
