<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('expense_categories')->onDelete('cascade');
            $table->string('vendor')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('payment_method'); // cash, card, bank_transfer
            $table->date('expense_date');
            $table->string('approval_status')->default('submitted'); // draft, submitted, approved, rejected, paid
            $table->foreignId('approved_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('receipt_attachment_path')->nullable();
            $table->text('note')->nullable();
            $table->boolean('is_recurring')->default(false);
            $table->foreignId('created_by_user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
