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
        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'shift_id')) {
                $table->foreignId('shift_id')->nullable()->constrained('shifts')->nullOnDelete()->after('created_by');
            }

            if (! Schema::hasColumn('orders', 'driver_id')) {
                $table->foreignId('driver_id')->nullable()->constrained('drivers')->nullOnDelete()->after('shift_id');
            }

            if (! Schema::hasColumn('orders', 'driver_commission')) {
                $table->decimal('driver_commission', 10, 2)->default(0)->after('driver_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'driver_commission')) {
                $table->dropColumn('driver_commission');
            }

            if (Schema::hasColumn('orders', 'driver_id')) {
                $table->dropForeign(['driver_id']);
                $table->dropColumn('driver_id');
            }

            if (Schema::hasColumn('orders', 'shift_id')) {
                $table->dropForeign(['shift_id']);
                $table->dropColumn('shift_id');
            }
        });
    }
};
