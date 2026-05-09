<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Shift;
use Illuminate\Support\Facades\Auth;

class ShiftManager extends Component
{
    public $showModal = false;
    public $opening_cash = 0;
    public $actual_cash = 0;
    public $activeShift;
    public array $shiftSummary = [];

    protected $listeners = [
        'openShiftManager' => 'openModal',
    ];

    public function mount()
    {
        $this->checkActiveShift();
    }

    public function openModal()
    {
        $this->checkActiveShift();
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function checkActiveShift()
    {
        $this->activeShift = Shift::where('user_id', Auth::id())
            ->whereNull('closed_at')
            ->first();

        if ($this->activeShift) {
            $this->loadShiftSummary();
        } else {
            $this->shiftSummary = [];
        }
    }

    public function openShift()
    {
        $this->validate([
            'opening_cash' => 'required|numeric|min:0.01',
        ], [
            'opening_cash.required' => 'الرجاء إدخال رصيد الافتتاح.',
            'opening_cash.numeric' => 'رصيد الافتتاح يجب أن يكون رقمًا.',
            'opening_cash.min' => 'رصيد الافتتاح يجب أن يكون أكبر من صفر.',
        ]);

        $this->activeShift = Shift::create([
            'user_id' => Auth::id(),
            'opened_at' => now(),
            'opening_cash' => $this->opening_cash,
            'expected_cash' => $this->opening_cash,
        ]);

        session()->flash('message', 'تم فتح الوردية بنجاح.');
        $this->showModal = false;
        $this->opening_cash = 0;
    }

    public function closeShift()
    {
        if (!$this->activeShift) {
            return;
        }

        $this->validate([
            'actual_cash' => 'required|numeric',
        ], [
            'actual_cash.required' => 'الرجاء إدخال الرصيد الفعلي عند الإغلاق.',
            'actual_cash.numeric' => 'الرصيد الفعلي يجب أن يكون رقمًا.',
        ]);

        $this->loadShiftSummary();
        $totalSales = $this->shiftSummary['total_sales'];
        $totalExpenses = $this->shiftSummary['total_expenses'];
        $expected = ($this->activeShift->opening_cash + $totalSales) - $totalExpenses;

        $this->activeShift->update([
            'closed_at' => now(),
            'expected_cash' => $expected,
            'actual_cash' => $this->actual_cash,
            'discrepancy' => $this->actual_cash - $expected,
            'total_sales' => $totalSales,
            'total_expenses' => $totalExpenses,
        ]);

        session()->flash('message', 'تم تقفيل الوردية بنجاح.');
        $this->showModal = false;
        $this->actual_cash = 0;
    }

    private function loadShiftSummary(): void
    {
        if (!$this->activeShift) {
            $this->shiftSummary = [];
            return;
        }

        $ordersQuery = $this->activeShift->orders()->where('status', 'completed');
        $totalSales = (float) (clone $ordersQuery)->sum('total');
        $totalOrders = (int) (clone $ordersQuery)->count();
        $deliveryOrders = (int) (clone $ordersQuery)->where('order_type', 'delivery')->count();
        $takeawayOrders = (int) (clone $ordersQuery)->where('order_type', 'takeaway')->count();
        $storeOrders = (int) (clone $ordersQuery)->where('order_type', 'store')->count();
        $totalExpenses = (float) $this->activeShift->expenses()->sum('amount');
        $netCash = (float) $this->activeShift->opening_cash + $totalSales - $totalExpenses;

        $this->shiftSummary = [
            'total_sales' => $totalSales,
            'total_orders' => $totalOrders,
            'delivery_orders' => $deliveryOrders,
            'takeaway_orders' => $takeawayOrders,
            'store_orders' => $storeOrders,
            'total_expenses' => $totalExpenses,
            'net_cash' => $netCash,
        ];
    }

    public function render()
    {
        return view('livewire.shift-manager');
    }
}