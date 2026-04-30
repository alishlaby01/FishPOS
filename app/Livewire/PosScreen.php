<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Component;

class PosScreen extends Component
{
    public string $search = '';
    public string $selectedCategory = 'all';
    public array $cart = [];
    public float $discount = 0;
    public float $deliveryFee = 0;
    public string $orderType = 'store';
    public ?string $customerName = null;
    public ?string $phone = null;
    public ?string $address = null;

    private ?float $subtotalCache = null;
    private ?float $finalTotalCache = null;

    public function mount(): void
    {
        $state = session('pos_cart_state', []);
        $this->cart = $this->normalizeCartFromState($state['cart'] ?? []);
        $this->discount = $this->normalizeMoney($state['discount'] ?? 0);
        $this->deliveryFee = $this->normalizeMoney($state['delivery_fee'] ?? 0);
        $this->orderType = $state['order_type'] ?? 'store';
        $this->customerName = $state['customer_name'] ?? null;
        $this->phone = $state['phone'] ?? null;
        $this->address = $state['address'] ?? null;
        $this->invalidateTotals();
    }

    public function saveAndPrint(): void { $this->storeOrder(true); }
    public function saveOrder(): void { $this->storeOrder(false); }

    private function storeOrder(bool $printRequested): void
    {
        $this->discount = $this->normalizeMoney($this->discount);
        $this->deliveryFee = $this->normalizeMoney($this->deliveryFee);
        $this->invalidateTotals();
        $this->validate($this->rules(), $this->validationMessages());

        $cartSnapshot = $this->validatedCartSnapshot();
        if ($cartSnapshot === []) {
            $this->addError('cart', 'لا يمكن حفظ طلب فارغ.');
            return;
        }

        $order = null;
        DB::transaction(function () use ($cartSnapshot, &$order) {
            $order = Order::query()->create([
                'invoice_number' => 'TMP-' . Str::uuid(),
                'order_type' => $this->orderType,
                'customer_name' => $this->customerName,
                'phone' => $this->phone,
                'address' => $this->address,
                'subtotal' => $this->subtotal,
                'discount' => $this->discount,
                'delivery_fee' => $this->deliveryFee,
                'total' => $this->finalTotal,
                'status' => 'completed',
                'created_by' => Auth::id() ?: 1, // يستخدم ID 1 لو مفيش Login
            ]);

            $order->invoice_number = $this->nextInvoiceNumber((int) $order->id);
            $order->save();

            $order->orderItems()->createMany(collect($cartSnapshot)->map(fn ($item) => [
                'product_id' => $item['product_id'],
                'quantity' => $item['qty'],
                'price' => $item['price'],
                'total' => $item['total'],
                'created_at' => now(),
                'updated_at' => now(),
            ])->all());
        });

        if ($printRequested && $order) {
            $this->dispatch('print-receipt', order: [
                'invoice_number' => $order->invoice_number,
                'date' => now()->format('Y-m-d H:i'),
                'subtotal' => $this->subtotal,
                'discount' => $this->discount,
                'total' => $this->finalTotal,
                'items' => array_values($cartSnapshot),
            ]);
        }

        $this->clearCart();
        session()->flash('success', 'تم تسجيل الطلب بنجاح.');
    }

    // ... باقي دوال الـ Helpers (recalculate, addProductFast, etc.) كما هي في ملفك الأصلي
    public function addProductFast($id, $name, $price) {
        if (isset($this->cart[$id])) { $this->cart[$id]['qty']++; }
        else { $this->cart[$id] = ['product_id' => $id, 'name' => $name, 'price' => $price, 'qty' => 1]; }
        $this->cart[$id]['total'] = $this->cart[$id]['price'] * $this->cart[$id]['qty'];
        $this->syncCartState();
    }
    public function increaseQty($id) { $this->cart[$id]['qty']++; $this->syncCartState(); }
    public function decreaseQty($id) { if($this->cart[$id]['qty'] > 1) $this->cart[$id]['qty']--; else unset($this->cart[$id]); $this->syncCartState(); }
    public function clearCart() { $this->cart = []; $this->discount = 0; $this->deliveryFee = 0; $this->invalidateTotals(); $this->persistState(); }
    public function syncCartState() { $this->invalidateTotals(); $this->persistState(); }
    private function invalidateTotals() { $this->subtotalCache = $this->finalTotalCache = null; }
    private function persistState() { session(['pos_cart_state' => ['cart' => $this->cart, 'discount' => $this->discount]]); }
    #[Computed] public function products() { return Product::where('active', true)->when($this->selectedCategory !== 'all', fn($q) => $q->whereHas('category', fn($c) => $c->where('slug', $this->selectedCategory)))->get(); }
    #[Computed] public function categories() { return Category::where('active', true)->get(); }
    public function getSubtotalProperty() { return collect($this->cart)->sum('total'); }
    public function getFinalTotalProperty() { return $this->subtotal - $this->discount + $this->deliveryFee; }
    private function normalizeMoney($v) { return max(0, (float)$v); }
    private function validatedCartSnapshot() { return $this->cart; }
    private function rules() { return ['discount' => 'numeric']; }
    private function validationMessages(): array { return []; }
    private function nextInvoiceNumber($id) { return 'INV-' . now()->format('Ymd') . '-' . $id; }
    public function render() { return view('livewire.pos-screen'); }


    /*
    |--------------------------------------------------------------------------
    | Helpers (Missing Methods)
    |--------------------------------------------------------------------------
    */

    private function normalizeCartFromState(array $cart): array
    {
        $normalized = [];

        foreach ($cart as $item) {
            if (! is_array($item) || ! isset($item['product_id'], $item['name'], $item['price'], $item['qty'])) {
                continue;
            }

            $productId = (int) $item['product_id'];
            $qty = max(1, (int) $item['qty']);
            $price = $this->normalizeMoney($item['price']);

            $normalized[$productId] = [
                'product_id' => $productId,
                'name' => (string) $item['name'],
                'price' => $price,
                'qty' => $qty,
                'total' => round($price * $qty, 2),
            ];
        }

        return $normalized;
    }

    private function calculateSubtotal(): float
    {
        return round(array_sum(array_column($this->cart, 'total')), 2);
    }

    private function calculateFinalTotal(float $subtotal, float $discount, float $deliveryFee): float
    {
        return max(0, round($subtotal - $discount + $deliveryFee, 2));
    }

}