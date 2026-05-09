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
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained(); // الكاشير اللي فاتح
            $table->dateTime('opened_at');
            $table->dateTime('closed_at')->nullable();
            $table->decimal('opening_cash', 10, 2); // الفلوس اللي بيبدأ بيها في الدرج
            $table->decimal('expected_cash', 10, 2)->default(0); // السيستم بيحسبها (مبيعات + رصيد أول)
            $table->decimal('actual_cash', 10, 2)->nullable(); // الكاشير عدهم بإيده
            $table->decimal('discrepancy', 10, 2)->nullable(); // الفرق (عجز أو زيادة)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
