<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('area_id')->constrained('dining_areas')->onDelete('cascade');
            $table->string('table_number');
            $table->string('qr_code')->nullable();
            $table->string('qr_url')->nullable();
            $table->integer('capacity')->default(4);
            $table->string('status')->default('available');
            $table->timestamps();

            $table->unique(['area_id', 'table_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tables');
    }
};
