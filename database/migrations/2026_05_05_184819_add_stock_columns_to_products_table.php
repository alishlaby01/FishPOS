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
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('current_stock', 10, 2)->default(0)->after('price');
            $table->decimal('min_stock', 10, 2)->default(5)->after('current_stock');
            $table->string('unit', 20)->default('كيلو')->after('min_stock');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['current_stock', 'min_stock', 'unit']);
        });
    }
};
