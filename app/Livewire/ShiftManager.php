<?php

namespace App\Livewire;

use App\Models\Shift;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;

class ShiftManager extends Component
{
    public $showModal = false;
    public $opening_cash = 0;
    public $actual_cash = 0;
    public $activeShift;
    public array $shiftSummary = [];

    public function mount()
    {
        $this->checkActiveShift();
    }

    #[On('openShiftManager')]
    public function openModal(): void
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

        try {
            $created = DB::transaction(function () {
                // وردية مفتوحة = closed_at فارغ
                $hasOpenShift = Shift::query()
                    ->where('user_id', Auth::id())
                    ->whereNull('closed_at')
                    ->lockForUpdate()
                    ->exists();

                if ($hasOpenShift) {
                    return null;
                }

                return Shift::create([
                    'user_id' => Auth::id(),
                    'opened_at' => now(),
                    'opening_cash' => $this->opening_cash,
                    'expected_cash' => $this->opening_cash,
                ]);
            });

            if ($created === null) {
                $this->dispatch('toast', ['message' => 'لديك وردية مفتوحة بالفعل', 'type' => 'error']);
                $this->checkActiveShift();
                return;
            }

            $this->activeShift = $created;
            session()->flash('message', 'تم فتح الوردية بنجاح.');
            $this->showModal = false;
            $this->opening_cash = 0;
        } catch (\Throwable $e) {
            Log::error('ShiftManager::openShift failed', ['exception' => $e]);
            $this->dispatch('toast', ['message' => 'تعذر فتح الوردية، يرجى المحاولة مرة أخرى.', 'type' => 'error']);
        }
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

        try {
            $shiftId = $this->activeShift->id;

            DB::transaction(function () use ($shiftId) {
                $this->loadShiftSummary();
                $totalSales = $this->shiftSummary['total_sales'];
                $totalExpenses = $this->shiftSummary['total_expenses'];

                $shift = Shift::query()
                    ->whereKey($shiftId)
                    ->where('user_id', Auth::id())
                    ->whereNull('closed_at')
                    ->lockForUpdate()
                    ->first();

                if (!$shift) {
                    throw new \RuntimeException('Shift already closed or not found.');
                }

                $expected = ($shift->opening_cash + $totalSales) - $totalExpenses;

                $shift->update([
                    'closed_at' => now(),
                    'expected_cash' => $expected,
                    'actual_cash' => $this->actual_cash,
                    'discrepancy' => $this->actual_cash - $expected,
                    'total_sales' => $totalSales,
                    'total_expenses' => $totalExpenses,
                ]);
            });

            $this->dispatch('toast', ['message' => 'تم تقفيل الوردية بنجاح.', 'type' => 'success']);

            $this->showModal = false;
            $this->actual_cash = 0;
            $this->checkActiveShift();

            $landingRoute = match (Auth::user()?->role) {
                'cashier' => 'cashier-dashboard',
                'owner' => 'owner-dashboard',
                default => 'home',
            };

            session()->flash('success', 'تم تقفيل الوردية بنجاح.');

            return $this->redirect(route($landingRoute), navigate: false);
        } catch (\Throwable $e) {
            Log::error('ShiftManager::closeShift failed', ['exception' => $e]);
            $this->dispatch('toast', ['message' => 'تعذر إغلاق الوردية، يرجى المحاولة مرة أخرى.', 'type' => 'error']);
        }
    }

    private function loadShiftSummary(): void
    {
        if (!$this->activeShift) {
            $this->shiftSummary = [];
            return;
        }

        $ordersQuery = $this->activeShift->orders()->where('status', 'completed');
        $totalSales = (float) (clone $ordersQuery)->sum('total');
        $goodsNetSales = (float) (clone $ordersQuery)->sum(DB::raw('subtotal - COALESCE(discount, 0)'));
        $deliveryFeesTotal = (float) (clone $ordersQuery)->sum('delivery_fee');
        $totalOrders = (int) (clone $ordersQuery)->count();
        $deliveryOrders = (int) (clone $ordersQuery)->where('order_type', 'delivery')->count();
        $takeawayOrders = (int) (clone $ordersQuery)->where('order_type', 'takeaway')->count();
        $storeOrders = (int) (clone $ordersQuery)->where('order_type', 'store')->count();
        $totalExpenses = (float) $this->activeShift->expenses()->sum('amount');
        $netCash = (float) $this->activeShift->opening_cash + $totalSales - $totalExpenses;

        $this->shiftSummary = [
            'total_sales' => $totalSales,
            'goods_net_sales' => $goodsNetSales,
            'delivery_fees_total' => $deliveryFeesTotal,
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