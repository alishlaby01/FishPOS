<?php

namespace App\Livewire;

use App\Exceptions\ShiftClosedException;
use App\Models\{Category, Driver, Order, Product, Shift, StockEntry};
use Illuminate\Support\Facades\{Auth, DB, Log};
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Component;

class PosScreen extends Component
{
    // --- الشاشات والبحث ---
    public string $search = '';
    public string $selectedCategory = 'all';

    // --- بيانات السلة والطلب ---
    public array $cart = [];
    public array $quantities = []; // للكميات المُدخلة يدويًا
    public $discount = 0;
    public $deliveryFee = 0;
    public string $orderType = 'store';

    // --- بيانات العميل والطيار ---
    public ?string $customerName = null;
    public ?string $phone = null;
    public ?string $address = null;
    public ?int $driverId = null;
    public $driverCommission = 0;
    public string $newDriverName = '';
    public string $newDriverPhone = '';
    public array $customerRecentOrders = [];

    /**
     * تهيئة المكون عند التحميل
     */
    public function mount(): void
    {
        // 1) التحقق من وجود وردية فعالة
        if (!$this->checkActiveShift()) {
            return;
        }

        // 2) استعادة حالة السلة من الجلسة
        $state = session('pos_cart_state', []);
        $this->loadState($state);
    }

    /**
     * Toolbar زر «إدارة الوردية» يرسل الحدث openShiftManager؛ مكوّن ShiftManager يستمع عبر #[On('openShiftManager')]
     * فيفتح المودال مع ملخص الوردية إن وُجدت شفت مفتوحة، أو نموذج الفتح إن لم توجد.
     */
    private function checkActiveShift(): bool
    {
        $hasShift = Shift::where('user_id', Auth::id())
            ->whereNull('closed_at')
            ->exists();

        if (!$hasShift) {
            session()->flash('error', 'برجاء فتح وردية أولاً.');
            $this->dispatch('openShiftManager');
            return false;
        }

        return true;
    }

    // --- العمليات الحسابية (Computed) ---

    #[Computed]
    public function subtotal(): float
    {
        return (float) collect($this->cart)->sum('total');
    }

    #[Computed]
    public function finalTotal(): float
    {
        return max(0, $this->subtotal() - $this->discount + $this->deliveryFee);
    }

    #[Computed]
    public function products()
    {
        return Product::query()
            ->where('active', true)
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->when($this->selectedCategory !== 'all', function ($q) {
                $q->whereHas('category', fn($c) => $c->where('slug', $this->selectedCategory));
            })->get();
    }

    #[Computed]
    public function categories()
    {
        return Category::where('active', true)->get();
    }

    #[Computed]
    public function drivers()
    {
        return Driver::where('is_active', true)->get();
    }

    #[Computed]
    public function selectedDriverDetails(): ?Driver
    {
        if (!$this->driverId) {
            return null;
        }

        return Driver::where('is_active', true)->find($this->driverId);
    }

    #[Computed]
    public function deliveryOrders()
    {
        return Order::query()
            ->with('driver')
            ->where('order_type', 'delivery')
            ->latest()
            ->limit(8)
            ->get();
    }

    #[Computed]
    public function readyOrders()
    {
        return Order::query()
            ->where('status', 'completed')
            ->where('order_type', '!=', 'delivery')
            ->latest()
            ->limit(8)
            ->get();
    }

    // --- إدارة السلة (Actions) ---

    public function addProductFast($id, $name = null, $price = null)
    {
        $product = Product::query()
            ->where('active', true)
            ->find($id);

        if (!$product) {
            $this->addError('order', 'هذا المنتج غير متاح الآن.');
            return;
        }

        $defaultQty = $product->product_type === 'weight' ? 0.5 : 1;

        if (isset($this->cart[$id])) {
            $this->cart[$id]['qty'] += $defaultQty;
        } else {
            $this->cart[$id] = [
                'product_id' => $id,
                'name' => $product->name,
                'price' => (float) $product->price,
                'qty' => $defaultQty,
                'product_type' => $product->product_type,
            ];
        }

        // تهيئة الكمية في quantities
        $this->quantities[$id] = $this->cart[$id]['qty'];

        $this->updateItemTotal($id);
    }

    public function increaseQty($id)
    {
        if (isset($this->cart[$id])) {
            $increment = $this->cart[$id]['product_type'] === 'weight' ? 0.1 : 1;
            $this->cart[$id]['qty'] += $increment;
            $this->quantities[$id] = $this->cart[$id]['qty'];
            $this->updateItemTotal($id);
        }
    }

    public function decreaseQty($id)
    {
        if (isset($this->cart[$id])) {
            $decrement = $this->cart[$id]['product_type'] === 'weight' ? 0.1 : 1;
            if ($this->cart[$id]['qty'] > $decrement) {
                $this->cart[$id]['qty'] -= $decrement;
                $this->quantities[$id] = $this->cart[$id]['qty'];
                $this->updateItemTotal($id);
            } else {
                unset($this->cart[$id]);
                unset($this->quantities[$id]);
            }
            $this->persistState();
        }
    }

    public function updateQty($id, $qty)
    {
        if (isset($this->cart[$id]) && $qty > 0) {
            $this->cart[$id]['qty'] = (float) $qty;
            $this->updateItemTotal($id);
        } elseif ($qty <= 0) {
            unset($this->cart[$id]);
        }
        $this->persistState();
    }

    public function updatedQuantities($value, $key)
    {
        // $key سيكون مثل "123" (id المنتج)
        $id = (int) $key;
        $qty = (float) $value;

        if (isset($this->cart[$id])) {
            if ($qty > 0) {
                $this->cart[$id]['qty'] = $qty;
                $this->updateItemTotal($id);
            } else {
                unset($this->cart[$id]);
                unset($this->quantities[$id]);
            }
            $this->persistState();
        }
    }

    private function updateItemTotal($id)
    {
        $this->cart[$id]['total'] = $this->cart[$id]['price'] * $this->cart[$id]['qty'];
        $this->persistState();
    }

    // --- حفظ الطلبات ---

    public function saveAndPrint() 
{ 
    $this->storeOrder(print: true); 
}

public function saveOrder() 
{ 
    $this->storeOrder(print: false); 
}

    public function updatedOrderType($value): void
    {
        if ($value !== 'delivery') {
            $this->driverId = null;
            $this->driverCommission = 0;
            $this->deliveryFee = 0;
            $this->customerRecentOrders = [];
        }

        $this->persistState();
    }

    public function updatedDeliveryFee(): void
    {
        $this->persistState();
    }

    public function updatedPhone($value): void
    {
        if ($this->orderType !== 'delivery') {
            return;
        }

        $phone = trim((string) $value);
        if (mb_strlen($phone) < 6) {
            $this->customerRecentOrders = [];
            return;
        }

        $lastOrder = Order::query()
            ->where('order_type', 'delivery')
            ->where(function ($q) use ($phone) {
                $q->where('phone', $phone)
                    ->orWhere('phone', 'like', "%{$phone}%");
            })
            ->latest()
            ->first();

        if ($lastOrder) {
            $this->customerName = $this->customerName ?: $lastOrder->customer_name;
            $this->address = $this->address ?: $lastOrder->address;
            $this->driverId = $this->driverId ?: $lastOrder->driver_id;
        }

        $this->customerRecentOrders = Order::query()
            ->where('order_type', 'delivery')
            ->where(function ($q) use ($phone) {
                $q->where('phone', $phone)
                    ->orWhere('phone', 'like', "%{$phone}%");
            })
            ->latest()
            ->limit(5)
            ->get(['invoice_number', 'created_at', 'total', 'address'])
            ->map(fn ($order) => [
                'invoice_number' => $order->invoice_number,
                'created_at' => optional($order->created_at)->format('Y-m-d h:i A'),
                'total' => (float) $order->total,
                'address' => $order->address,
            ])->toArray();
    }

    public function useRecentAddress(string $invoiceNumber): void
    {
        if ($this->orderType !== 'delivery') {
            return;
        }

        $order = Order::query()
            ->where('invoice_number', $invoiceNumber)
            ->where('order_type', 'delivery')
            ->first();

        if (!$order) {
            return;
        }

        $this->address = $order->address;
        $this->customerName = $order->customer_name ?: $this->customerName;
        $this->driverId = $order->driver_id ?: $this->driverId;
        $this->persistState();
    }

    public function addDriver(): void
    {
        if (Auth::user()?->role !== 'owner') {
            abort(403);
        }

        $validated = $this->validate([
            'newDriverName' => 'required|string|min:2|max:255',
            'newDriverPhone' => 'nullable|string|max:50',
        ], [
            'newDriverName.required' => 'اكتب اسم الطيار أولاً.',
        ]);

        Driver::create([
            'name' => $validated['newDriverName'],
            'phone' => $validated['newDriverPhone'] ?: null,
            'is_active' => true,
        ]);

        $this->reset(['newDriverName', 'newDriverPhone']);
        session()->flash('success', 'تم إضافة الطيار بنجاح.');
    }

    public function deleteDriver(int $driverId): void
    {
        if (Auth::user()?->role !== 'owner') {
            abort(403);
        }

        $driver = Driver::find($driverId);
        if (!$driver) {
            return;
        }

        $hasOrders = Order::where('driver_id', $driver->id)->exists();

        if ($hasOrders) {
            $driver->update(['is_active' => false]);
            if ($this->driverId === $driver->id) {
                $this->driverId = null;
                $this->persistState();
            }
            session()->flash('error', 'لا يمكن حذف الطيار المرتبط بطلبات سابقة، تم تعطيله بدلاً من ذلك.');
            return;
        }

        if ($this->driverId === $driver->id) {
            $this->driverId = null;
            $this->persistState();
        }

        $driver->delete();
        session()->flash('success', 'تم حذف الطيار بنجاح.');
    }

    /**
     * Build order lines from DB prices and quantities in the cart (ignores client-supplied price/total).
     *
     * @param  float|null  $deliveryFeeApplied  مبلغ ثابت لرسوم التوصيل عند الدليفري؛ يُمرَّر من الطلب لتفادي أي تعارض مع الحالة.
     * @return array{items: list<array{product_id: int, name: string, price: float, qty: float, total: float, product_type: string}>, subtotal: float, total: float}|null
     */
    private function resolveCartLineItemsForOrder(?float $deliveryFeeApplied = null): ?array
    {
        $productIds = collect($this->cart)
            ->pluck('product_id')
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values()
            ->all();

        if (count($productIds) !== count($this->cart)) {
            $this->addError('order', 'بيانات السلة غير صالحة.');
            return null;
        }

        $products = Product::query()
            ->whereIn('id', $productIds)
            ->where('active', true)
            ->get()
            ->keyBy('id');

        $items = [];
        foreach ($this->cart as $row) {
            $productId = (int) ($row['product_id'] ?? 0);
            if ($productId <= 0) {
                $this->addError('order', 'بيانات السلة غير صالحة.');
                return null;
            }

            $qty = (float) ($row['qty'] ?? 0);
            if ($qty <= 0) {
                $this->addError('order', 'الكمية يجب أن تكون أكبر من صفر.');
                return null;
            }

            $product = $products->get($productId);
            if (!$product) {
                $this->addError('order', 'منتج غير متوفر أو تم إيقافه.');
                return null;
            }

            $unitPrice = (float) $product->price;
            $lineTotal = $unitPrice * $qty;

            $items[] = [
                'product_id'   => $product->id,
                'name'         => $product->name,
                'price'        => $unitPrice,
                'qty'          => $qty,
                'total'        => $lineTotal,
                'product_type' => $product->product_type,
            ];
        }

        $subtotal = (float) collect($items)->sum('total');
        $discount = (float) $this->discount;
        $deliveryFee = $deliveryFeeApplied !== null
            ? max(0, (float) $deliveryFeeApplied)
            : ($this->orderType === 'delivery' ? max(0, (float) $this->deliveryFee) : 0);
        $total = max(0, $subtotal - $discount + $deliveryFee);

        return [
            'items'    => $items,
            'subtotal' => $subtotal,
            'total'    => $total,
        ];
    }

    private function storeOrder(bool $print)
    {
        $this->validate([
            'discount'   => 'nullable|numeric|min:0',
            'deliveryFee' => 'nullable|numeric|min:0',
            'driverId'   => 'required_if:orderType,delivery|nullable|exists:drivers,id',
            'driverCommission' => 'nullable|numeric|min:0',
            'cart'       => 'required|array|min:1',
        ], [
            'cart.required' => 'السلة فارغة، أضف منتجات أولاً.',
            'driverId.required_if' => 'لا يمكن حفظ أو طباعة أوردر الدليفري بدون اختيار الطيار.',
        ]);

        if ($this->orderType === 'delivery') {
            $validDriver = Driver::where('id', $this->driverId)
                ->where('is_active', true)
                ->exists();

            if (!$validDriver) {
                $this->addError('driverId', 'الطيار المختار غير متاح حالياً، اختر طيارًا آخر.');
                return;
            }
        }

        $activeShift = Shift::where('user_id', Auth::id())->whereNull('closed_at')->first();

        if (!$activeShift) {
            $this->addError('order', 'انتهت جلستك أو تم إغلاق الوردية.');
            return;
        }

        $deliveryFeeForOrder = $this->orderType === 'delivery'
            ? max(0, (float) $this->deliveryFee)
            : 0;

        $resolved = $this->resolveCartLineItemsForOrder($deliveryFeeForOrder);
        if ($resolved === null) {
            return;
        }

        try {
            $order = DB::transaction(function () use ($activeShift, $resolved, $deliveryFeeForOrder) {
                $shift = Shift::query()
                    ->whereKey($activeShift->id)
                    ->whereNull('closed_at')
                    ->lockForUpdate()
                    ->first();

                if (!$shift) {
                    throw new ShiftClosedException();
                }

                // إنشاء الطلب
                $order = Order::create([
                    'invoice_number'    => 'TMP-' . Str::uuid(),
                    'order_type'        => $this->orderType,
                    'customer_name'     => $this->customerName,
                    'phone'             => $this->phone,
                    'address'           => $this->address,
                    'subtotal'          => $resolved['subtotal'],
                    'discount'          => $this->discount,
                    'delivery_fee'      => $deliveryFeeForOrder,
                    'total'             => $resolved['total'],
                    'status'            => 'completed',
                    'created_by'        => Auth::id(),
                    'shift_id'          => $shift->id,
                    'driver_id'         => $this->orderType === 'delivery' ? $this->driverId : null,
                    'driver_commission' => $this->orderType === 'delivery' ? $this->driverCommission : 0,
                ]);

                // تحديث رقم الفاتورة النهائي
                $order->update(['invoice_number' => $this->generateInvoiceNo($order->id)]);

                // تحديث كاش الوردية
                $shift->increment('expected_cash', $order->total);

                // بنود الطلب وحركات المخزن (أسعار وإجماليات من الخادم + قفل المنتج لتحديث المخزون)
                $itemsForStock = collect($resolved['items'])->sortBy('product_id')->values()->all();
                foreach ($itemsForStock as $item) {
                    $product = Product::query()
                        ->whereKey($item['product_id'])
                        ->where('active', true)
                        ->lockForUpdate()
                        ->first();

                    if (!$product) {
                        throw new \RuntimeException('Product unavailable');
                    }

                    $order->orderItems()->create([
                        'product_id' => $item['product_id'],
                        'quantity'   => $item['qty'],
                        'price'      => $item['price'],
                        'total'      => $item['total'],
                    ]);

                    StockEntry::create([
                        'product_id' => $item['product_id'],
                        'quantity'   => $item['qty'],
                        'type'       => 'out',
                        'note'       => "بيع فاتورة: {$order->invoice_number}",
                    ]);

                    $currentStock = (float) ($product->getRawOriginal('current_stock') ?? 0);
                    $product->update([
                        'current_stock' => max(0, $currentStock - (float) $item['qty']),
                    ]);
                }

                return $order;
            });

            if ($print) {
                $this->dispatch('print-receipt', order: [
                    'invoice_number' => $order->invoice_number,
                    'created_at'     => $order->created_at?->format('Y-m-d H:i'),
                    'order_type'     => $order->order_type,
                    'customer_name'  => $order->customer_name,
                    'phone'          => $order->phone,
                    'address'        => $order->address,
                    'total'          => $resolved['total'],
                    'discount'       => (float) $this->discount,
                    'delivery_fee'   => $deliveryFeeForOrder,
                    'subtotal'       => $resolved['subtotal'],
                    'items'          => $resolved['items'],
                ]);
            }

            $this->resetForm();
            session()->flash('success', 'تم تسجيل الطلب بنجاح ✅');

        } catch (ShiftClosedException $e) {
            Log::warning('PosScreen::storeOrder shift closed during transaction', ['exception' => $e]);
            $this->dispatch('toast', ['message' => 'تم إغلاق الوردية ولا يمكن إتمام البيع.', 'type' => 'error']);
        } catch (\Throwable $e) {
            Log::error('PosScreen::storeOrder failed', ['exception' => $e]);
            $this->dispatch('toast', ['message' => 'تعذر إتمام الطلب، يرجى المحاولة مرة أخرى.', 'type' => 'error']);
            $this->addError('order', 'تعذر إتمام الطلب، يرجى المحاولة مرة أخرى.');
        }
    }

    // --- Helpers ---

    private function resetForm()
    {
        $this->reset(['cart', 'discount', 'deliveryFee', 'orderType', 'driverId', 'driverCommission', 'customerName', 'phone', 'address', 'customerRecentOrders']);
        $this->orderType = 'store';
        $this->driverCommission = 0;
        $this->persistState();
    }

    private function persistState()
    {
        session(['pos_cart_state' => [
            'cart'          => $this->cart,
            'quantities'    => $this->quantities,
            'discount'      => $this->discount,
            'delivery_fee'  => $this->deliveryFee,
            'order_type'    => $this->orderType,
            'driver_id'     => $this->driverId,
            'driver_commission' => $this->driverCommission,
            'customer_name' => $this->customerName,
            'phone'         => $this->phone,
            'address'       => $this->address,
        ]]);
    }

    private function loadState(array $state)
    {
        $this->cart         = $this->normalizeCart($state['cart'] ?? []);
        $this->quantities   = $state['quantities'] ?? [];
        $this->discount     = (float) ($state['discount'] ?? 0);
        $this->deliveryFee  = (float) ($state['delivery_fee'] ?? 0);
        $this->orderType    = $state['order_type'] ?? 'store';
        $this->driverId     = isset($state['driver_id']) ? (int) $state['driver_id'] : null;
        $this->driverCommission = (float) ($state['driver_commission'] ?? 0);
        $this->customerName = $state['customer_name'] ?? null;
        $this->phone        = $state['phone'] ?? null;
        $this->address      = $state['address'] ?? null;

        if ($this->orderType === 'delivery' && $this->phone) {
            $this->updatedPhone($this->phone);
        }
    }

    private function normalizeCart(array $cart): array
    {
        $normalized = [];
        $this->quantities = [];
        foreach ($cart as $item) {
            if (isset($item['product_id'])) {
                $normalized[$item['product_id']] = $item;
                $this->quantities[$item['product_id']] = $item['qty'];
            }
        }
        return $normalized;
    }

    private function generateInvoiceNo($id): string
    {
        return 'INV-' . now()->format('ymd') . '-' . str_pad($id, 4, '0', STR_PAD_LEFT);
    }

    public function render()
    {
        return view('livewire.pos-screen');
    }
}