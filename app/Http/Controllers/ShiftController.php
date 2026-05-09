<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class ShiftController extends Controller
{
    public function open(Request $request): RedirectResponse
    {
        $request->validate([
            'opening_balance' => 'required|numeric|min:0',
        ]);

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
        ]);

        return redirect()->back()->with('success', 'تم فتح الوردية بنجاح! ✅');
    }

    public function close(Shift $shift): RedirectResponse
    {
        // التحقق من أن الوردية تخص المستخدم الحالي
        if ($shift->user_id !== auth()->id()) {
            abort(403);
        }

        // التحقق من أن الوردية مفتوحة
        if ($shift->closed_at) {
            return redirect()->back()->withErrors(['shift' => 'الوردية مغلقة بالفعل']);
        }

        // حساب إجمالي المبيعات والمصروفات
        $totalSales = $shift->orders()->sum('total');
        $totalExpenses = $shift->expenses()->sum('amount');

        $shift->update([
            'closed_at' => now(),
            'expected_cash' => $shift->opening_cash + $totalSales - $totalExpenses,
            'total_sales' => $totalSales,
            'total_expenses' => $totalExpenses,
        ]);

        return redirect()->back()->with('success', 'تم إغلاق الوردية بنجاح! ✅');
    }
}
