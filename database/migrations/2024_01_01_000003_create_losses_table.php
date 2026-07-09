<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('losses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->date('loss_date');
            $table->decimal('quantity', 10, 3);
            $table->string('unit')->default('Stk');
            $table->string('reason'); // verderb, diebstahl, ablauf, beschaedigung, sonstiges
            $table->string('supplier')->nullable();
            $table->decimal('purchase_price', 10, 2)->nullable();
            $table->string('photo_path')->nullable();
            $table->text('notes')->nullable();
            $table->string('immutable_hash')->nullable(); // GoBD compliance
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('losses');
    }
};
