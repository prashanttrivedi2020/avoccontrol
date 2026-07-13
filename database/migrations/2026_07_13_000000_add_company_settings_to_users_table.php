<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('company_name')->nullable()->after('store_name');
            $table->string('owner_name')->nullable()->after('company_name');
            $table->string('address')->nullable()->after('owner_name');
            $table->string('tax_number')->nullable()->after('address');
            $table->string('logo_path')->nullable()->after('tax_number');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['company_name', 'owner_name', 'address', 'tax_number', 'logo_path']);
        });
    }
};
