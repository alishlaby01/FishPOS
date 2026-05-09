<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shifts', function (Blueprint $table) {
            if (!Schema::hasColumn('shifts', 'total_sales')) {
                $table->decimal('total_sales', 10, 2)->default(0)->after('discrepancy');
            }

            if (!Schema::hasColumn('shifts', 'total_expenses')) {
                $table->decimal('total_expenses', 10, 2)->default(0)->after('total_sales');
            }
        });
    }

    public function down(): void
    {
        Schema::table('shifts', function (Blueprint $table) {
            if (Schema::hasColumn('shifts', 'total_expenses')) {
                $table->dropColumn('total_expenses');
            }

            if (Schema::hasColumn('shifts', 'total_sales')) {
                $table->dropColumn('total_sales');
            }
        });
    }
};

