<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ShiftController;
use App\Livewire\Stock\MorningEntry;
use App\Models\Expense;
use App\Models\Order;
use App\Models\Shift;
use App\Models\StockEntry;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// === مسارات المصادقة ===
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// === مسارات محمية بالمصادقة ===
Route::middleware('auth')->group(function () {

    // Owner Dashboard
    Route::get('/owner-dashboard', function () {
        $user = request()->user();
        abort_unless($user && $user->role === 'owner', 403);

        return view('owner-dashboard');
    })->name('owner-dashboard');

    // Cashier Dashboard
    Route::get('/cashier-dashboard', function () {
        $user = request()->user();
        abort_unless($user && $user->role === 'cashier', 403);

        return view('cashier-dashboard');
    })->name('cashier-dashboard');

    // شاشة الكاشير
    Route::get('/cashier', function () {
        $user = request()->user();
        abort_unless($user && in_array($user->role, ['owner', 'cashier']), 403);

        return view('cashier');
    })->name('cashier');

    // شاشة التقارير (للمالك فقط)
    Route::get('/summary', function (Request $request) {
        $user = request()->user();
        abort_unless($user && $user->role === 'owner', 403);

        $dateInput = $request->input('date');
        if ($dateInput && preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateInput)) {
            try {
                $date = Carbon::createFromFormat('Y-m-d', $dateInput)->toDateString();
            } catch (Exception $e) {
                $date = today()->toDateString();
            }
        } else {
            $date = today()->toDateString();
        }

        $selectedShiftId = is_numeric($request->input('shift_id')) ? (int) $request->input('shift_id') : null;

        $shiftOptions = Shift::with('user')
            ->whereDate('closed_at', $date)
            ->whereNotNull('closed_at')
            ->orderBy('closed_at', 'desc')
            ->get();

        $selectedShift = null;
        $shiftReports = $shiftOptions;
        if ($selectedShiftId) {
            $selectedShift = $shiftOptions->firstWhere('id', $selectedShiftId);
            if ($selectedShift) {
                $shiftReports = collect([$selectedShift]);
            }
        }

        $ordersQuery = Order::query()->whereDate('created_at', $date);
        $expensesQuery = Expense::query()->whereDate('created_at', $date);

        if ($selectedShift) {
            $ordersQuery->where('shift_id', $selectedShift->id);
            $expensesQuery->where('shift_id', $selectedShift->id);
        }

        // إجمالي التحصيل حسب الفلتر
        $sales = (clone $ordersQuery)
            ->selectRaw('SUM(total) as total_sales, COUNT(*) as total_orders, SUM(discount) as total_discounts')
            ->first();

        $deliveryOrdersCount = (clone $ordersQuery)->where('order_type', 'delivery')->count();
        $takeawayOrdersCount = (clone $ordersQuery)->where('order_type', 'takeaway')->count();
        $storeOrdersCount = (clone $ordersQuery)->where('order_type', 'store')->count();

        // إجمالي المصاريف
        $expenses = $expensesQuery->sum('amount');

        // صافي الربح (بدون تكلفة شراء — تم إلغاء سعر الشراء من النظام)
        $netProfit = ($sales->total_sales ?? 0) - $expenses;

        // تقرير الهالك
        $wasteReport = StockEntry::where('type', 'waste')
            ->whereDate('created_at', $date)
            ->with('product')
            ->selectRaw('product_id, SUM(quantity) as total_waste, 
                        (SELECT note FROM stock_entries se2 WHERE se2.product_id = stock_entries.product_id AND se2.type = "waste" AND DATE(se2.created_at) = ? GROUP BY note ORDER BY COUNT(*) DESC LIMIT 1) as most_common_reason', [$date])
            ->groupBy('product_id')
            ->orderBy('total_waste', 'desc')
            ->limit(5)
            ->get();

        $summary = (object) [
            'total_sales' => $sales->total_sales ?? 0,
            'total_orders' => $sales->total_orders ?? 0,
            'total_discounts' => $sales->total_discounts ?? 0,
            'total_cost' => 0,
            'total_expenses' => $expenses,
            'net_profit' => $netProfit,
            'delivery_orders_count' => $deliveryOrdersCount,
            'takeaway_orders_count' => $takeawayOrdersCount,
            'store_orders_count' => $storeOrdersCount,
        ];

        $reportDate = Carbon::createFromFormat('Y-m-d', $date)->format('d/m/Y');

        return view('summary', compact('summary', 'wasteReport', 'shiftReports', 'shiftOptions', 'selectedShift', 'date', 'reportDate'));
    })->name('summary')->middleware('auth');

    // شاشة إدخال المخزون الصباحي
    Route::get('/morning-stock', MorningEntry::class)->name('morning-stock')->middleware('auth');

    // إدارة المنتجات (للمالك فقط)
    Route::get('/products', function () {
        $user = request()->user();
        abort_unless($user && $user->role === 'owner', 403);

        return view('products');
    })->name('products')->middleware('auth');

    // إدارة المصاريف
    Route::get('/expenses', function () {
        $user = request()->user();
        abort_unless($user && in_array($user->role, ['owner', 'cashier']), 403);

        return view('expenses');
    })->name('expenses')->middleware('auth');

    // إدارة الطلبات
    Route::get('/orders', function () {
        $user = request()->user();
        abort_unless($user && in_array($user->role, ['owner', 'cashier']), 403);

        return view('orders');
    })->name('orders')->middleware('auth');

    // إدارة الورديات
    Route::prefix('shifts')->name('shifts.')->group(function () {
        Route::post('/open', [ShiftController::class, 'open'])->name('open');
        Route::patch('/{shift}/close', [ShiftController::class, 'close'])->name('close');
    });
});
// In your web.php or wherever you route to POS
Route::get('/pos', function () {
    return view('layouts.app', ['slot' => view('livewire.pos-screen')]);
})->name('pos');


#للتجربة فقط طباعو ريسيت  
Route::get('/test-print', function () {
    return view('print_test');
});