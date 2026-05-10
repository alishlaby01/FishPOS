<div class="min-h-screen bg-slate-950 text-slate-100" dir="rtl">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <div class="mb-6 rounded-2xl border border-slate-800 bg-gradient-to-l from-slate-900 to-slate-800 p-5 shadow-xl">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h1 class="text-2xl font-extrabold text-white">شاشة الكاشير</h1>
                    <p class="mt-1 text-sm text-slate-400">اختَر المنتجات، عدّل الطلب، ثم احفظ أو احفظ واطبع الفاتورة.</p>
                </div>
                <div class="w-full max-w-md">
                    <input
                        wire:model.live.debounce.300ms="search"
                        type="text"
                        placeholder="ابحث باسم المنتج..."
                        class="w-full rounded-xl border border-slate-700 bg-slate-900 px-4 py-2.5 text-sm text-white placeholder-slate-500 outline-none ring-0 transition focus:border-indigo-500"
                    />
                </div>
            </div>
            <div class="mt-4 flex flex-wrap items-center gap-2 border-t border-slate-700/70 pt-4">
                <a href="{{ route('home') }}" class="rounded-lg bg-slate-800 px-3 py-1.5 text-xs font-semibold text-slate-200 transition hover:bg-slate-700">
                    الرئيسية
                </a>
                <a href="{{ route('cashier') }}" class="rounded-lg bg-slate-800 px-3 py-1.5 text-xs font-semibold text-slate-200 transition hover:bg-slate-700">
                    شاشة الكاشير
                </a>
                <a href="{{ route('orders') }}" class="rounded-lg bg-slate-800 px-3 py-1.5 text-xs font-semibold text-slate-200 transition hover:bg-slate-700">
                    كل الطلبات
                </a>
                <a href="{{ route('expenses') }}" class="rounded-lg bg-slate-800 px-3 py-1.5 text-xs font-semibold text-slate-200 transition hover:bg-slate-700">
                    المصروفات
                </a>
                <button
                    type="button"
                    wire:click="$dispatch('openShiftManager')"
                    class="rounded-lg bg-amber-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm ring-1 ring-amber-500/30 transition hover:bg-amber-500"
                >
                    إدارة الوردية
                </button>
                @if(auth()->user()?->role === 'owner')
                    <a href="{{ route('products') }}" class="rounded-lg bg-slate-800 px-3 py-1.5 text-xs font-semibold text-slate-200 transition hover:bg-slate-700">
                        المنتجات والأسعار
                    </a>
                @endif
                <form method="POST" action="{{ route('logout') }}" class="mr-auto">
                    @csrf
                    <button type="submit" class="rounded-lg bg-red-900/30 px-3 py-1.5 text-xs font-semibold text-red-300 transition hover:bg-red-900/60">
                        تسجيل الخروج
                    </button>
                </form>
            </div>
        </div>

        @if (session()->has('success'))
            <div class="mb-4 rounded-xl border border-emerald-700/50 bg-emerald-900/30 px-4 py-3 text-sm text-emerald-300">
                {{ session('success') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-4 rounded-xl border border-red-700/50 bg-red-900/30 px-4 py-3 text-sm text-red-300">
                {{ session('error') }}
            </div>
        @endif

        @error('order')
            <div class="mb-4 rounded-xl border border-red-700/50 bg-red-900/30 px-4 py-3 text-sm text-red-300">
                {{ $message }}
            </div>
        @enderror

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <section class="lg:col-span-2 rounded-2xl border border-slate-800 bg-slate-900/80 p-5 shadow-xl">
                <div class="mb-4 flex flex-wrap items-center gap-2">
                    <button
                        type="button"
                        wire:click="$set('selectedCategory', 'all')"
                        class="rounded-lg px-3 py-1.5 text-sm transition {{ $selectedCategory === 'all' ? 'bg-indigo-600 text-white' : 'bg-slate-800 text-slate-300 hover:bg-slate-700' }}"
                    >
                        كل الأقسام
                    </button>
                    @foreach($this->categories as $category)
                        <button
                            type="button"
                            wire:click="$set('selectedCategory', '{{ $category->slug }}')"
                            class="rounded-lg px-3 py-1.5 text-sm transition {{ $selectedCategory === $category->slug ? 'bg-indigo-600 text-white' : 'bg-slate-800 text-slate-300 hover:bg-slate-700' }}"
                        >
                            {{ $category->name }}
                        </button>
                    @endforeach
                </div>

                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 xl:grid-cols-4">
                    @forelse($this->products as $product)
                        <button
                            type="button"
                            wire:click="addProductFast({{ $product->id }}, @js($product->name), {{ (float) $product->price }})"
                            class="group rounded-xl border border-slate-800 bg-slate-900 p-3 text-right transition hover:border-indigo-500 hover:bg-slate-800"
                        >
                            <div class="mb-2 flex items-start justify-between gap-2">
                                <h3 class="line-clamp-2 text-sm font-bold text-white">{{ $product->name }}</h3>
                                @if(isset($cart[$product->id]))
                                    <span class="rounded-full bg-indigo-600 px-2 py-0.5 text-xs font-bold text-white">
                                        {{ $cart[$product->id]['qty'] }}
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs text-slate-400">{{ $product->category?->name ?? 'بدون تصنيف' }} • {{ $product->product_type === 'weight' ? 'وزني' : 'قطعي' }}</p>
                            <p class="mt-2 text-sm font-extrabold text-emerald-400">{{ number_format((float) $product->price, 2) }} ج.م</p>
                        </button>
                    @empty
                        <div class="col-span-full rounded-xl border border-slate-800 bg-slate-900 p-6 text-center text-slate-400">
                            لا توجد منتجات مطابقة للبحث الحالي.
                        </div>
                    @endforelse
                </div>
            </section>

            <section class="rounded-2xl border border-slate-800 bg-slate-900/80 p-5 shadow-xl">
                <div class="mb-4 flex flex-wrap items-start justify-between gap-3">
                    <h2 class="text-lg font-bold text-white">الطلب الحالي</h2>
                    <button
                        type="button"
                        wire:click="$dispatch('openShiftManager')"
                        class="rounded-lg bg-amber-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm ring-1 ring-amber-500/30 transition hover:bg-amber-500"
                    >
                        إدارة الوردية
                    </button>
                </div>

                <div class="space-y-2">
                    @forelse($cart as $id => $item)
                        <div class="rounded-xl border border-slate-800 bg-slate-900 p-3">
                            <div class="mb-2 flex items-center justify-between gap-2">
                                <h3 class="text-sm font-semibold text-white">{{ $item['name'] }}</h3>
                                <span class="text-xs text-slate-400">{{ number_format((float) $item['price'], 2) }} ج.م</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <button type="button" wire:click="decreaseQty({{ $id }})" class="h-7 w-7 rounded-md bg-slate-800 text-sm text-white transition hover:bg-slate-700">-</button>
                                    <input type="number" wire:model.live="quantities.{{ $id }}" 
                                           step="any" 
                                           min="0.01" 
                                           class="w-16 text-center text-sm font-bold bg-slate-800 border border-slate-700 rounded px-2 py-1 focus:border-indigo-500 outline-none" />
                                    <button type="button" wire:click="increaseQty({{ $id }})" class="h-7 w-7 rounded-md bg-indigo-600 text-sm text-white transition hover:bg-indigo-500">+</button>
                                </div>
                                <span class="text-sm font-bold text-emerald-400">{{ number_format((float) $item['total'], 2) }} ج.م</span>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-xl border border-dashed border-slate-700 p-5 text-center text-sm text-slate-500">
                            السلة فارغة، اختَر منتجات من القائمة.
                        </div>
                    @endforelse
                </div>

                <div class="mt-5 space-y-3 border-t border-slate-800 pt-4">
                    <div class="grid grid-cols-1 gap-2">
                        <input wire:model.live="discount" type="number" min="0" step="0.01" placeholder="خصم على الأصناف" class="rounded-lg border border-slate-700 bg-slate-900 px-3 py-2 text-sm outline-none focus:border-indigo-500" />
                    </div>

                    <div class="grid grid-cols-3 gap-2 text-xs">
                        <button type="button" wire:click="$set('orderType', 'store')" class="rounded-lg border px-2 py-2 transition {{ $orderType === 'store' ? 'border-indigo-500 bg-indigo-600/20 text-indigo-300' : 'border-slate-700 text-slate-400 hover:bg-slate-800' }}">داخل المحل</button>
                        <button type="button" wire:click="$set('orderType', 'takeaway')" class="rounded-lg border px-2 py-2 transition {{ $orderType === 'takeaway' ? 'border-indigo-500 bg-indigo-600/20 text-indigo-300' : 'border-slate-700 text-slate-400 hover:bg-slate-800' }}">تيك أواي</button>
                        <button type="button" wire:click="$set('orderType', 'delivery')" class="rounded-lg border px-2 py-2 transition {{ $orderType === 'delivery' ? 'border-indigo-500 bg-indigo-600/20 text-indigo-300' : 'border-slate-700 text-slate-400 hover:bg-slate-800' }}">دليفري</button>
                    </div>

                    @if($orderType === 'delivery')
                        <div class="space-y-2 rounded-xl border border-indigo-800/40 bg-indigo-900/10 p-3">
                            <label class="block text-xs font-semibold text-indigo-200">رسوم التوصيل (ج.م)</label>
                            <input wire:model.live="deliveryFee" type="number" min="0" step="0.01" placeholder="مثال: 50" class="w-full rounded-lg border border-indigo-600/50 bg-slate-900 px-3 py-2 text-sm font-semibold text-white outline-none focus:border-indigo-400" />
                            <input wire:model.live="customerName" type="text" placeholder="اسم العميل" class="w-full rounded-lg border border-slate-700 bg-slate-900 px-3 py-2 text-sm outline-none focus:border-indigo-500" />
                            <input wire:model.live.debounce.400ms="phone" type="text" placeholder="رقم الهاتف" class="w-full rounded-lg border border-slate-700 bg-slate-900 px-3 py-2 text-sm outline-none focus:border-indigo-500" />
                            <input wire:model.live="address" type="text" placeholder="العنوان" class="w-full rounded-lg border border-slate-700 bg-slate-900 px-3 py-2 text-sm outline-none focus:border-indigo-500" />
                            <div class="grid grid-cols-2 gap-2">
                                <select wire:model.live="driverId" class="rounded-lg border border-slate-700 bg-slate-900 px-3 py-2 text-sm outline-none focus:border-indigo-500">
                                    <option value="">اختَر الطيار</option>
                                    @foreach($this->drivers as $driver)
                                        <option value="{{ $driver->id }}">{{ $driver->name }} - {{ $driver->phone ?: 'بدون رقم' }}</option>
                                    @endforeach
                                </select>
                                <input wire:model.live="driverCommission" type="number" min="0" step="0.01" placeholder="عمولة الطيار" class="rounded-lg border border-slate-700 bg-slate-900 px-3 py-2 text-sm outline-none focus:border-indigo-500" />
                            </div>
                            @if($this->selectedDriverDetails)
                                <div class="rounded-lg border border-slate-700 bg-slate-900/70 p-2 text-xs text-slate-300">
                                    <p>اسم الطيار: <span class="font-bold text-white">{{ $this->selectedDriverDetails->name }}</span></p>
                                    <p class="mt-1">موبايل الطيار: <span class="font-bold text-emerald-300">{{ $this->selectedDriverDetails->phone ?: 'غير متوفر' }}</span></p>
                                </div>
                            @endif

                            @if(!empty($customerRecentOrders))
                                <div class="rounded-lg border border-emerald-800/40 bg-emerald-900/10 p-2">
                                    <p class="mb-2 text-xs font-bold text-emerald-300">آخر طلبات هذا العميل</p>
                                    <div class="space-y-1">
                                        @foreach($customerRecentOrders as $recentOrder)
                                            <div class="rounded-md bg-slate-900/80 px-2 py-1 text-xs text-slate-300">
                                                <div class="flex items-center justify-between gap-2">
                                                    <span class="font-semibold text-white">{{ $recentOrder['invoice_number'] }}</span>
                                                    <span class="text-emerald-300">{{ number_format((float) $recentOrder['total'], 2) }} ج.م</span>
                                                </div>
                                                <p class="mt-1 text-slate-400">{{ $recentOrder['created_at'] }}</p>
                                                @if(!empty($recentOrder['address']))
                                                    <p class="mt-1 line-clamp-1 text-slate-500">{{ $recentOrder['address'] }}</p>
                                                    <button
                                                        type="button"
                                                        wire:click="useRecentAddress('{{ $recentOrder['invoice_number'] }}')"
                                                        class="mt-2 rounded-md bg-indigo-900/40 px-2 py-1 text-[11px] font-semibold text-indigo-300 hover:bg-indigo-900/70"
                                                    >
                                                        استخدام هذا العنوان
                                                    </button>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                            @error('driverId')
                                <p class="text-xs text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif

                    <div class="space-y-1 rounded-xl border border-slate-800 bg-slate-900 p-3 text-sm">
                        <div class="flex items-center justify-between text-slate-400">
                            <span>مجموع الأصناف</span>
                            <span>{{ number_format($this->subtotal, 2) }} ج.م</span>
                        </div>
                        @if((float) $discount > 0)
                            <div class="flex items-center justify-between text-slate-400">
                                <span>الخصم</span>
                                <span>− {{ number_format((float) $discount, 2) }} ج.م</span>
                            </div>
                        @endif
                        @if($orderType === 'delivery')
                            <div class="flex items-center justify-between text-amber-300/90">
                                <span>رسوم التوصيل</span>
                                <span>{{ number_format((float) $deliveryFee, 2) }} ج.م</span>
                            </div>
                        @endif
                        <div class="flex items-center justify-between border-t border-slate-700 pt-2 text-base font-extrabold text-white">
                            <span>الإجمالي النهائي</span>
                            <span class="text-emerald-400">{{ number_format($this->finalTotal, 2) }} ج.م</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <button
                            type="button"
                            wire:click="saveOrder"
                            wire:loading.attr="disabled"
                            class="rounded-xl bg-slate-800 px-3 py-2.5 text-sm font-bold text-white transition hover:bg-slate-700 disabled:opacity-50"
                        >
                            حفظ فقط
                        </button>
                        <button
                            type="button"
                            wire:click="saveAndPrint"
                            wire:loading.attr="disabled"
                            class="rounded-xl bg-indigo-600 px-3 py-2.5 text-sm font-bold text-white transition hover:bg-indigo-500 disabled:opacity-50"
                        >
                            حفظ وطباعة
                        </button>
                    </div>
                </div>
            </section>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
            @if(auth()->user()?->role === 'owner')
                <section class="rounded-2xl border border-slate-800 bg-slate-900/80 p-5 shadow-xl lg:col-span-2">
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="text-base font-bold text-white">إدارة الطيارين (للأدمن)</h3>
                        <span class="text-xs text-slate-400">أضف أو احذف طيار للتوصيل</span>
                    </div>
                    <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
                        <input wire:model.live="newDriverName" type="text" placeholder="اسم الطيار" class="rounded-lg border border-slate-700 bg-slate-900 px-3 py-2 text-sm outline-none focus:border-indigo-500" />
                        <input wire:model.live="newDriverPhone" type="text" placeholder="موبايل الطيار" class="rounded-lg border border-slate-700 bg-slate-900 px-3 py-2 text-sm outline-none focus:border-indigo-500" />
                        <button type="button" wire:click="addDriver" class="rounded-lg bg-indigo-600 px-3 py-2 text-sm font-bold text-white transition hover:bg-indigo-500">
                            إضافة طيار
                        </button>
                    </div>
                    @error('newDriverName')
                        <p class="mt-2 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                    <div class="mt-4 grid grid-cols-1 gap-2 md:grid-cols-2 lg:grid-cols-3">
                        @forelse($this->drivers as $driver)
                            <div class="flex items-center justify-between rounded-xl border border-slate-800 bg-slate-900 p-3">
                                <div>
                                    <p class="text-sm font-bold text-white">{{ $driver->name }}</p>
                                    <p class="text-xs text-slate-400">{{ $driver->phone ?: 'بدون رقم' }}</p>
                                </div>
                                <button
                                    type="button"
                                    wire:click="deleteDriver({{ $driver->id }})"
                                    onclick="return confirm('هل تريد حذف هذا الطيار؟ إذا كان مرتبط بطلبات سابقة سيتم تعطيله فقط.')"
                                    class="rounded-md bg-red-900/30 px-2 py-1 text-xs font-bold text-red-300 hover:bg-red-900/60"
                                >
                                    حذف
                                </button>
                            </div>
                        @empty
                            <div class="rounded-xl border border-dashed border-slate-700 p-4 text-center text-sm text-slate-500">
                                لا يوجد طيارون مضافون حاليًا.
                            </div>
                        @endforelse
                    </div>
                </section>
            @endif

            <section class="rounded-2xl border border-slate-800 bg-slate-900/80 p-5 shadow-xl">
                <div class="mb-3 flex items-center justify-between">
                    <h3 class="text-base font-bold text-white">أوردرات الدليفري</h3>
                    <a href="{{ route('orders') }}" class="text-xs text-indigo-300 hover:text-indigo-200">عرض الكل</a>
                </div>
                <div class="space-y-2">
                    @forelse($this->deliveryOrders as $order)
                        <div class="rounded-xl border border-slate-800 bg-slate-900 p-3 text-sm">
                            <div class="flex items-center justify-between">
                                <p class="font-bold text-white">{{ $order->invoice_number }}</p>
                                <span class="rounded-full bg-indigo-900/40 px-2 py-0.5 text-xs text-indigo-300">دليفري</span>
                            </div>
                            <p class="mt-1 text-xs text-slate-300">العميل: {{ $order->customer_name ?: 'غير مسجل' }}</p>
                            <p class="mt-1 text-xs text-slate-400">الطيار: {{ $order->driver?->name ?: 'غير محدد' }}</p>
                            <p class="mt-1 text-xs text-emerald-400">الإجمالي: {{ number_format((float) $order->total, 2) }} ج.م</p>
                        </div>
                    @empty
                        <div class="rounded-xl border border-dashed border-slate-700 p-4 text-center text-sm text-slate-500">
                            لا توجد أوردرات دليفري حتى الآن.
                        </div>
                    @endforelse
                </div>
            </section>

            <section class="rounded-2xl border border-slate-800 bg-slate-900/80 p-5 shadow-xl">
                <div class="mb-3 flex items-center justify-between">
                    <h3 class="text-base font-bold text-white">الأوردرات الجاهزة</h3>
                    <a href="{{ route('orders') }}" class="text-xs text-indigo-300 hover:text-indigo-200">عرض الكل</a>
                </div>
                <div class="space-y-2">
                    @forelse($this->readyOrders as $order)
                        <div class="rounded-xl border border-slate-800 bg-slate-900 p-3 text-sm">
                            <div class="flex items-center justify-between">
                                <p class="font-bold text-white">{{ $order->invoice_number }}</p>
                                <span class="rounded-full bg-emerald-900/40 px-2 py-0.5 text-xs text-emerald-300">جاهز</span>
                            </div>
                            <p class="mt-1 text-xs text-slate-400">نوع الطلب: {{ $order->order_type }}</p>
                            <p class="mt-1 text-xs text-slate-400">الوقت: {{ $order->created_at?->format('h:i A') }}</p>
                            <p class="mt-1 text-xs text-emerald-400">الإجمالي: {{ number_format((float) $order->total, 2) }} ج.م</p>
                        </div>
                    @empty
                        <div class="rounded-xl border border-dashed border-slate-700 p-4 text-center text-sm text-slate-500">
                            لا توجد أوردرات جاهزة حاليًا.
                        </div>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</div>

@script
<script>
    if (!window.__fishPosReceiptPrinterInit) {
        window.__fishPosReceiptPrinterInit = true;

        const orderTypeLabel = (type) => {
            if (type === 'delivery') return 'دليفري';
            if (type === 'takeaway') return 'تيك أواي';
            return 'داخل المحل';
        };

        const escapeHtml = (value) => {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        };

        Livewire.on('print-receipt', ({ order }) => {
            if (!order) return;

            const items = Array.isArray(order.items) ? order.items : [];
            const itemsRows = items.map((item) => {
                const name = escapeHtml(item.name ?? '');
                const qty = Number(item.qty ?? 0);
                const price = Number(item.price ?? 0).toFixed(2);
                const total = Number(item.total ?? 0).toFixed(2);
                return `
                    <tr>
                        <td class="item-name">${name}</td>
                        <td class="item-center">${qty}</td>
                        <td class="item-center">${price}</td>
                        <td class="item-right">${total}</td>
                    </tr>
                `;
            }).join('');

            const deliveryFee = Number(order.delivery_fee ?? order.deliveryFee ?? 0);
            const isDelivery = order.order_type === 'delivery';
            const deliveryRow = isDelivery && deliveryFee > 0
                ? `<tr class="delivery-fee-row">
                        <td class="item-name">رسوم التوصيل</td>
                        <td class="item-center">—</td>
                        <td class="item-center">—</td>
                        <td class="item-right">${deliveryFee.toFixed(2)}</td>
                   </tr>`
                : '';

            const printWindow = window.open('', '_blank', 'width=420,height=900');
            if (!printWindow) return;

            const html = `
                <!DOCTYPE html>
                <html lang="ar" dir="rtl">
                <head>
                    <meta charset="UTF-8" />
                    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
                    <title>فاتورة ${escapeHtml(order.invoice_number)}</title>
                    <style>
                        @page { size: 80mm auto; margin: 3mm; }
                        body { font-family: Tahoma, Arial, sans-serif; color: #000; margin: 0; background: #fff; }
                        .receipt { width: 74mm; margin: 0 auto; font-size: 12px; line-height: 1.4; }
                        .center { text-align: center; }
                        .sep { border-top: 1px dashed #000; margin: 6px 0; }
                        .title { font-size: 16px; font-weight: 700; }
                        .meta { font-size: 11px; }
                        .pill { display: inline-block; border: 1px solid #000; border-radius: 999px; padding: 1px 8px; font-size: 11px; margin-top: 4px; }
                        table { width: 100%; border-collapse: collapse; margin-top: 6px; }
                        th, td { padding: 3px 0; border-bottom: 1px dotted #999; }
                        th { font-size: 10px; }
                        .item-name { width: 40%; text-align: right; }
                        .item-center { width: 15%; text-align: center; }
                        .item-right { width: 30%; text-align: left; }
                        .delivery-fee-row td { font-style: italic; color: #333; }
                        .totals div { display: flex; justify-content: space-between; margin: 2px 0; }
                        .totals .final { font-weight: 700; font-size: 13px; }
                    </style>
                </head>
                <body onload="window.print(); setTimeout(() => window.close(), 300);">
                    <div class="receipt">
                        <div class="center">
                            <div class="title">Fish POS</div>
                            <div class="meta">فاتورة بيع</div>
                            <div class="pill">${orderTypeLabel(order.order_type)}</div>
                        </div>

                        <div class="sep"></div>
                        <div class="meta"><strong>رقم الفاتورة:</strong> ${escapeHtml(order.invoice_number)}</div>
                        <div class="meta"><strong>التاريخ:</strong> ${escapeHtml(order.created_at ?? '')}</div>
                        ${order.customer_name ? `<div class="meta"><strong>العميل:</strong> ${escapeHtml(order.customer_name)}</div>` : ''}
                        ${order.phone ? `<div class="meta"><strong>الهاتف:</strong> ${escapeHtml(order.phone)}</div>` : ''}
                        ${order.address ? `<div class="meta"><strong>العنوان:</strong> ${escapeHtml(order.address)}</div>` : ''}

                        <div class="sep"></div>
                        <table>
                            <thead>
                                <tr>
                                    <th class="item-name">الصنف</th>
                                    <th class="item-center">ك</th>
                                    <th class="item-center">سعر</th>
                                    <th class="item-right">إجمالي</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${itemsRows}
                                ${deliveryRow}
                            </tbody>
                        </table>

                        <div class="sep"></div>
                        <div class="totals">
                            <div><span>مجموع الأصناف</span><span>${Number(order.subtotal ?? 0).toFixed(2)} ج.م</span></div>
                            <div><span>الخصم</span><span>${Number(order.discount ?? 0).toFixed(2)} ج.م</span></div>
                            ${isDelivery ? `<div><span>رسوم التوصيل</span><span>${deliveryFee.toFixed(2)} ج.م</span></div>` : ''}
                            <div class="final"><span>الإجمالي المحصل</span><span>${Number(order.total ?? 0).toFixed(2)} ج.م</span></div>
                        </div>

                        <div class="sep"></div>
                        <div class="center meta">شكراً لزيارتكم</div>
                    </div>
                </body>
                </html>
            `;

            printWindow.document.open();
            printWindow.document.write(html);
            printWindow.document.close();
        });
    }
</script>
@endscript