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
        Schema::table('stock_entries', function (Blueprint $table) {
            if (! Schema::hasColumn('stock_entries', 'product_id')) {
                $table->foreignId('product_id')->constrained('products')->cascadeOnDelete()->after('id');
            }

            if (! Schema::hasColumn('stock_entries', 'quantity')) {
                $table->integer('quantity')->after('product_id');
            }

            if (! Schema::hasColumn('stock_entries', 'type')) {
                $table->enum('type', ['in', 'out'])->after('quantity');
            }

            if (! Schema::hasColumn('stock_entries', 'note')) {
                $table->string('note')->nullable()->after('type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_entries', function (Blueprint $table) {
            if (Schema::hasColumn('stock_entries', 'note')) {
                $table->dropColumn('note');
            }

            if (Schema::hasColumn('stock_entries', 'type')) {
                $table->dropColumn('type');
            }

            if (Schema::hasColumn('stock_entries', 'quantity')) {
                $table->dropColumn('quantity');
            }

            if (Schema::hasColumn('stock_entries', 'product_id')) {
                $table->dropForeign(['product_id']);
                $table->dropColumn('product_id');
            }
        });
    }
};
