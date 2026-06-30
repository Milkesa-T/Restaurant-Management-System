<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dining_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->foreignId('table_id')->nullable()->constrained('tables')->onDelete('set null');
            $table->string('status', 20)->default('active'); // active, completed, cancelled
            $table->timestamp('opened_at')->useCurrent();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('branch_id', 'idx_ds_branch');
            $table->index('table_id', 'idx_ds_table');
            $table->index('status', 'idx_ds_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dining_sessions');
    }
};
