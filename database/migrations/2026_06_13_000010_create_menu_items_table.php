<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('menu_categories')->onDelete('cascade');
            $table->foreignId('kitchen_station_id')->nullable()->constrained('kitchen_stations')->onDelete('set null');
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('image_path')->nullable();
            $table->integer('estimated_prep_time')->default(15); // in minutes
            $table->string('availability_status')->default('available');
            
            // Chef proposal & Admin approval flow
            $table->string('status', 30)->default('published'); // proposed, approved, published
            $table->foreignId('proposed_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->decimal('proposed_price', 10, 2)->nullable();
            $table->foreignId('approved_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
