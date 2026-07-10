<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // User-specific units
            $table->string('name'); // e.g., 'Stk', 'kg', 'L', etc.
            $table->integer('sort_order')->default(0); // For ordering in dropdown
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Each user can have one unit with a given name
            $table->unique(['user_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
