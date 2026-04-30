<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () { return view('welcome'); });

Route::middleware('auth.basic')->group(function () {
    
    // شاشة الكاشير
    Route::get('/cashier', function () {
        $user = request()->user();
        abort_unless($user && in_array($user->role, ['owner', 'cashier']), 403);
        return view('cashier');
    })->name('cashier');

    // شاشة التقارير
    Route::get('/summary', function () {
        $user = request()->user();
        abort_unless($user && $user->role === 'owner', 403);
    
        $summary = \App\Models\Order::whereDate('created_at', today())
            ->selectRaw('COUNT(*) as total_orders, SUM(total) as total_sales, SUM(discount) as total_discounts')
            ->first();
    
        return view('summary', compact('summary'));
    })->name('summary');
});