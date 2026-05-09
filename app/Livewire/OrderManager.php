<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class OrderManager extends Component
{
    public $search = '';
    public $statusFilter = '';
    public $dateFilter = '';
    public $orders;
    public $selectedOrder;

    public function mount()
    {
        $this->loadOrders();
    }

    public function loadOrders()
    {
        $this->orders = Order::with(['orderItems.product', 'creator'])
            ->when($this->search, fn($query) => $query->where('invoice_number', 'like', '%' . $this->search . '%'))
            ->when($this->statusFilter, fn($query) => $query->where('status', $this->statusFilter))
            ->when($this->dateFilter, fn($query) => $query->whereDate('created_at', $this->dateFilter))
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function updatedSearch()
    {
        $this->loadOrders();
    }

    public function updatedStatusFilter()
    {
        $this->loadOrders();
    }

    public function updatedDateFilter()
    {
        $this->loadOrders();
    }

    public function printOrder($orderId)
    {
        $order = Order::with(['orderItems.product', 'creator'])->find($orderId);
        $this->dispatch('print-order', ['order' => $order->toArray()]);
    }

    public function viewOrder($orderId)
    {
        $this->selectedOrder = Order::with(['orderItems.product', 'creator'])->find($orderId);
    }

    public function closeOrderModal()
    {
        $this->selectedOrder = null;
    }

    public function printDailyReport()
    {
        $date = $this->dateFilter ?: today()->format('Y-m-d');
        $orders = Order::with(['orderItems.product'])
            ->whereDate('created_at', $date)
            ->get();

        $totalSales = $orders->sum('total');
        $totalOrders = $orders->count();

        $this->dispatch('print-daily-report', [
            'date' => $date,
            'orders' => $orders,
            'totalSales' => $totalSales,
            'totalOrders' => $totalOrders
        ]);
    }

    public function render()
    {
        return view('livewire.order-manager');
    }
}