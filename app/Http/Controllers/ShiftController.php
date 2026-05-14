<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ShiftController extends Controller
{
    public function open(Request $request): RedirectResponse
    {
        $request->validate([
            'opening_balance' => 'required|numeric|min:0',
        ]);

        try {
            // التحقق من عدم وجود وردية مفتوحة
            $existingShift = Shift::where('user_id', auth()->id())
                ->whereNull('closed_at')
                ->first();

            if ($existingShift) {
                return redirect()->back()->withErrors(['shift' => 'لديك وردية مفتوحة بالفعل']);
            }

            Shift::create([
                'user_id' => auth()->id(),
                'opened_at' => now(),
                'opening_cash' => $request->opening_balance,
                'expected_cash' => $request->opening_balance,
            ]);

            return redirect()->back()->with('success', 'تم فتح الوردية بنجاح! ✅');
        } catch (\Throwable $e) {
            Log::error('ShiftController::open failed', ['exception' => $e]);

            return redirect()->back()->withErrors(['shift' => 'تعذر فتح الوردية، يرجى المحاولة مرة أخرى.']);
        }
    }

    public function close(Request $request, Shift $shift): RedirectResponse
    {
        // التحقق من أن الوردية تخص المستخدم الحالي
        if ($shift->user_id !== auth()->id()) {
            abort(403);
        }

        // التحقق من أن الوردية مفتوحة
        if ($shift->closed_at) {
            return redirect()->back()->withErrors(['shift' => 'الوردية مغلقة بالفعل']);
        }

        $request->validate([
            'actual_cash' => 'required|numeric|min:0',
        ]);

        try {
            // نفس منطق ShiftManager: مبيعات الطلبات المكتملة فقط
            $ordersQuery = $shift->orders()->where('status', 'completed');
            $totalSales = (float) (clone $ordersQuery)->sum('total');
            $totalExpenses = (float) $shift->expenses()->sum('amount');
            $expectedCash = $shift->opening_cash + $totalSales - $totalExpenses;
            $actualCash = (float) $request->input('actual_cash');

            $shift->update([
                'closed_at' => now(),
                'expected_cash' => $expectedCash,
                'actual_cash' => $actualCash,
                'discrepancy' => $actualCash - $expectedCash,
                'total_sales' => $totalSales,
                'total_expenses' => $totalExpenses,
            ]);

            return redirect()->back()->with('success', 'تم إغلاق الوردية بنجاح! ✅');
        } catch (\Throwable $e) {
            Log::error('ShiftController::close failed', ['exception' => $e]);

            return redirect()->back()->withErrors(['shift' => 'تعذر إغلاق الوردية، يرجى المحاولة مرة أخرى.']);
        }
    }
}
