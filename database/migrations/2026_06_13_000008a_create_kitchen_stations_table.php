<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kitchen_stations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->string('name');
            $table->string('status')->default('active'); // active, inactive
            $table->timestamps();
            
            $table->unique(['branch_id', 'name']);
        });

        Schema::create('user_kitchen_stations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('station_id')->constrained('kitchen_stations')->onDelete('cascade');
            $table->boolean('is_primary')->default(true);
            $table->timestamp('assigned_at')->useCurrent();
            $table->foreignId('approved_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'station_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_kitchen_stations');
        Schema::dropIfExists('kitchen_stations');
    }
};
