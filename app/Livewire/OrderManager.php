<?php

namespace App\Livewire;

use App\Models\Order;
use App\Models\Shift;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;

class OrderManager extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $dateFilter = '';
    public $selectedOrder;

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedDateFilter()
    {
        $this->resetPage();
    }

    public function printOrder($orderId)
    {
        try {
            $order = Order::with(['orderItems.product', 'creator'])->find($orderId);
            if (!$order) {
                $this->dispatch('toast', ['message' => 'الطلب غير موجود.', 'type' => 'error']);
                return;
            }

            $this->dispatch('print-order', ['order' => $order->toArray()]);
        } catch (\Throwable $e) {
            Log::error('OrderManager::printOrder failed', ['exception' => $e]);
            $this->dispatch('toast', ['message' => 'تعذر طباعة الطلب، يرجى المحاولة مرة أخرى.', 'type' => 'error']);
        }
    }

    public function viewOrder($orderId)
    {
        try {
            $this->selectedOrder = Order::with(['orderItems.product', 'creator'])->find($orderId);
        } catch (\Throwable $e) {
            Log::error('OrderManager::viewOrder failed', ['exception' => $e]);
            $this->dispatch('toast', ['message' => 'تعذر تحميل الطلب، يرجى المحاولة مرة أخرى.', 'type' => 'error']);
        }
    }

    public function closeOrderModal()
    {
        $this->selectedOrder = null;
    }

    public function cancelOrder(int $orderId): void
    {
        if (Auth::user()?->role !== 'owner') {
            abort(403);
        }

        try {
            $order = Order::find($orderId);
            if (!$order) {
                $this->dispatch('toast', ['message' => 'الطلب غير موجود.', 'type' => 'error']);
                return;
            }

            if ($order->status === 'cancelled') {
                $this->dispatch('toast', ['message' => 'الطلب ملغي مسبقاً.', 'type' => 'error']);
                return;
            }

            $order->update(['status' => 'cancelled']);
            $this->dispatch('toast', ['message' => 'تم إلغاء الطلب.', 'type' => 'success']);

            if ($this->selectedOrder && (int) $this->selectedOrder->id === $orderId) {
                $this->selectedOrder = Order::with(['orderItems.product', 'creator'])->find($orderId);
            }
        } catch (\Throwable $e) {
            Log::error('OrderManager::cancelOrder failed', ['exception' => $e]);
            $this->dispatch('toast', ['message' => 'تعذر إلغاء الطلب، يرجى المحاولة مرة أخرى.', 'type' => 'error']);
        }
    }

    public function printDailyReport()
    {
        try {
            $date = $this->dateFilter ?: today()->format('Y-m-d');
            $activeShift = Shift::query()
                ->where('user_id', Auth::id())
                ->whereNull('closed_at')
                ->first();

            $ordersQuery = Order::with(['orderItems.product'])
                ->whereDate('created_at', $date);

            if ($activeShift) {
                $ordersQuery->where('shift_id', $activeShift->id);
            } elseif (Auth::user()?->role === 'cashier') {
                $ordersQuery->whereRaw('1 = 0');
            }

            $orders = $ordersQuery->get();

            $totalSales = $orders->sum('total');
            $totalOrders = $orders->count();

            $this->dispatch('print-daily-report', [
                'date' => $date,
                'orders' => $orders,
                'totalSales' => $totalSales,
                'totalOrders' => $totalOrders,
                'scoped_to_shift' => (bool) $activeShift,
            ]);
        } catch (\Throwable $e) {
            Log::error('OrderManager::printDailyReport failed', ['exception' => $e]);
            $this->dispatch('toast', ['message' => 'تعذر طباعة التقرير، يرجى المحاولة مرة أخرى.', 'type' => 'error']);
        }
    }

    public function render()
    {
        $activeShift = Shift::query()
            ->where('user_id', Auth::id())
            ->whereNull('closed_at')
            ->first();

        $orders = Order::with(['orderItems.product', 'creator'])
            ->when($activeShift, fn ($query) => $query->where('shift_id', $activeShift->id))
            ->when(! $activeShift && Auth::user()?->role === 'cashier', fn ($query) => $query->whereRaw('1 = 0'))
            ->when($this->search, fn ($query) => $query->where('invoice_number', 'like', '%' . $this->search . '%'))
            ->when($this->statusFilter, fn ($query) => $query->where('status', $this->statusFilter))
            ->when($this->dateFilter, fn ($query) => $query->whereDate('created_at', $this->dateFilter))
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('livewire.order-manager', compact('orders', 'activeShift'));
    }
}