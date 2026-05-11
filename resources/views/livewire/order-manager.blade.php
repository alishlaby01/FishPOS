<div class="p-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">إدارة الطلبات</h3>
        <button wire:click="printDailyReport" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            طباعة التقرير اليومي 📊
        </button>
    </div>

    @if(isset($activeShift) && $activeShift)
        <div class="mb-4 rounded-lg border border-indigo-200 bg-indigo-50 px-4 py-3 text-sm text-indigo-900 dark:border-indigo-800 dark:bg-indigo-950/40 dark:text-indigo-100">
            يتم عرض طلبات <strong>الوردية الحالية المفتوحة</strong> فقط (يمكنك إغلاق الوردية لفتح نطاق أوسع إذا كنت مالكاً وتعمل بدون شفت).
        </div>
    @elseif(auth()->user()?->role === 'cashier')
        <div class="mb-4 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900 dark:border-amber-800 dark:bg-amber-950/40 dark:text-amber-100">
            لا توجد وردية مفتوحة — افتح وردية من لوحة الكاشير أو شاشة البيع لعرض الطلبات هنا.
        </div>
    @endif

    <!-- فلاتر البحث -->
    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">البحث برقم الفاتورة</label>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="ابحث برقم الفاتورة..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">الحالة</label>
                <select wire:model.live="statusFilter" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">جميع الحالات</option>
                    <option value="pending">في الانتظار</option>
                    <option value="processing">قيد التجهيز</option>
                    <option value="completed">مكتمل</option>
                    <option value="cancelled">ملغي</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">التاريخ</label>
                <input wire:model.live="dateFilter" type="date" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </div>
    </div>

    <!-- جدول الطلبات -->
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">رقم الفاتورة</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">التاريخ</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">الحالة</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">التوصيل</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">الإجمالي</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">الكاشير</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">الإجراءات</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                @forelse($orders as $order)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                        {{ $order->invoice_number }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                        {{ $order->created_at->format('d/m/Y H:i') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                            @if($order->status === 'completed') bg-green-100 text-green-800
                            @elseif($order->status === 'processing') bg-yellow-100 text-yellow-800
                            @elseif($order->status === 'pending') bg-blue-100 text-blue-800
                            @else bg-red-100 text-red-800 @endif">
                            @switch($order->status)
                                @case('completed') مكتمل @break
                                @case('processing') قيد التجهيز @break
                                @case('pending') في الانتظار @break
                                @case('cancelled') ملغي @break
                                @default {{ $order->status }} @endswitch
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-200">
                        {{ number_format((float) $order->delivery_fee, 2) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                        {{ number_format($order->total, 2) }} جنيه مصري
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                        {{ $order->creator->name ?? 'غير معروف' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex flex-wrap items-center gap-2">
                            <button type="button" wire:click="printOrder({{ $order->id }})" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                🖨️ طباعة
                            </button>
                            <button type="button" wire:click="viewOrder({{ $order->id }})" class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-300">
                                👁️ عرض
                            </button>
                            @auth
                                @if(auth()->user()->role === 'owner' && $order->status !== 'cancelled')
                                    <button type="button"
                                        wire:click="cancelOrder({{ $order->id }})"
                                        wire:confirm="هل أنت متأكد من إلغاء هذا الطلب؟ لا يمكن التراجع عن الإلغاء من الواجهة."
                                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                        إلغاء
                                    </button>
                                @endif
                            @endauth
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                        لا توجد طلبات
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $orders->links() }}
    </div>

    <!-- Modal لعرض تفاصيل الطلب -->
    @if(isset($selectedOrder))
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" id="order-modal">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">تفاصيل الطلب: {{ $selectedOrder->invoice_number }}</h3>
                    <button type="button" wire:click="closeOrderModal" class="text-gray-400 hover:text-gray-600">
                        <span class="text-2xl">&times;</span>
                    </button>
                </div>
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <strong>التاريخ:</strong> {{ $selectedOrder->created_at->format('d/m/Y H:i') }}
                        </div>
                        <div>
                            <strong>الحالة:</strong> {{ $selectedOrder->status }}
                        </div>
                        <div>
                            <strong>الكاشير:</strong> {{ $selectedOrder->creator->name ?? 'غير معروف' }}
                        </div>
                        <div>
                            <strong>مجموع الأصناف:</strong> {{ number_format((float) $selectedOrder->subtotal, 2) }} جنيه مصري
                        </div>
                        <div>
                            <strong>الخصم:</strong> {{ number_format((float) $selectedOrder->discount, 2) }} جنيه مصري
                        </div>
                        @if($selectedOrder->order_type === 'delivery')
                            <div>
                                <strong>رسوم التوصيل:</strong> {{ number_format((float) $selectedOrder->delivery_fee, 2) }} جنيه مصري
                            </div>
                        @endif
                        <div>
                            <strong>الإجمالي المحصل:</strong> {{ number_format($selectedOrder->total, 2) }} جنيه مصري
                        </div>
                    </div>
                    <div>
                        <h4 class="font-medium mb-2">تفاصيل المنتجات:</h4>
                        <table class="min-w-full border">
                            <thead>
                                <tr>
                                    <th class="border px-4 py-2">المنتج</th>
                                    <th class="border px-4 py-2">الكمية</th>
                                    <th class="border px-4 py-2">السعر</th>
                                    <th class="border px-4 py-2">الإجمالي</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($selectedOrder->orderItems as $item)
                                <tr>
                                    <td class="border px-4 py-2">{{ $item->product->name }}</td>
                                    <td class="border px-4 py-2">{{ $item->quantity }}</td>
                                    <td class="border px-4 py-2">{{ number_format($item->price, 2) }}</td>
                                    <td class="border px-4 py-2">{{ number_format($item->total, 2) }}</td>
                                </tr>
                                @endforeach
                                @if((float) $selectedOrder->delivery_fee > 0)
                                <tr class="bg-amber-50 dark:bg-amber-900/20">
                                    <td class="border px-4 py-2 font-medium" colspan="3">رسوم التوصيل</td>
                                    <td class="border px-4 py-2 font-semibold">{{ number_format((float) $selectedOrder->delivery_fee, 2) }}</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="mt-6 flex flex-wrap justify-end gap-2 border-t border-gray-200 pt-4 dark:border-gray-600">
                    <button type="button" wire:click="printOrder({{ $selectedOrder->id }})"
                        class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                        🖨️ طباعة هذه الفاتورة
                    </button>
                    <button type="button" wire:click="closeOrderModal"
                        class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">
                        إغلاق
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@script
<script>
    if (! window.__fishPosOrderManagerPrintInit) {
        window.__fishPosOrderManagerPrintInit = true;

        const escapeHtml = (value) => String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');

        const openPrintWindow = (bodyHtml, title) => {
            const printWindow = window.open('', '_blank', 'width=900,height=900');
            if (! printWindow) {
                window.alert('تعذر فتح نافذة الطباعة. اسمح بالنوافذ المنبثقة لهذا الموقع.');
                return;
            }
            const doc = `<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>${escapeHtml(title)}</title>
    <style>
        @page { margin: 12mm; }
        body { font-family: Tahoma, Arial, sans-serif; color: #000; background: #fff; margin: 0; padding: 16px; }
    </style>
</head>
<body onload="window.print(); setTimeout(() => window.close(), 400);">
${bodyHtml}
</body>
</html>`;
            printWindow.document.open();
            printWindow.document.write(doc);
            printWindow.document.close();
        };

        Livewire.on('print-order', (payload) => {
            const order = payload?.order ?? payload;
            if (! order) {
                return;
            }

            const items = order.order_items || order.orderItems || [];
            const deliveryFee = Number(order.delivery_fee ?? order.deliveryFee ?? 0);
            const isDelivery = order.order_type === 'delivery';
            const subtotal = Number(order.subtotal ?? 0);
            const discount = Number(order.discount ?? 0);
            let rows = '';
            items.forEach((item) => {
                const pname = escapeHtml(item.product?.name ?? '—');
                rows += `
            <tr>
                <td style="border: 1px solid #ccc; padding: 8px;">${pname}</td>
                <td style="border: 1px solid #ccc; padding: 8px; text-align: center;">${escapeHtml(item.quantity)}</td>
                <td style="border: 1px solid #ccc; padding: 8px; text-align: right;">${Number(item.total ?? 0).toFixed(2)} ج.م</td>
            </tr>`;
            });
            if (isDelivery && deliveryFee > 0) {
                rows += `
            <tr style="font-style: italic;">
                <td style="border: 1px solid #ccc; padding: 8px;" colspan="2">رسوم التوصيل</td>
                <td style="border: 1px solid #ccc; padding: 8px; text-align: right;">${deliveryFee.toFixed(2)} ج.م</td>
            </tr>`;
            }

            const creatorName = escapeHtml(order.creator?.name ?? order.creator_name ?? 'غير معروف');
            const inv = escapeHtml(order.invoice_number ?? '');
            const body = `
        <div style="font-family: Tahoma, Arial, sans-serif; max-width: 420px; margin: 0 auto; padding: 8px;" dir="rtl">
            <h2 style="text-align: center; margin-bottom: 16px;">فاتورة الطلب</h2>
            <p><strong>رقم الفاتورة:</strong> ${inv}</p>
            <p><strong>التاريخ:</strong> ${escapeHtml(new Date(order.created_at).toLocaleString('ar-EG'))}</p>
            <p><strong>الكاشير:</strong> ${creatorName}</p>
            <hr style="margin: 16px 0;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th style="border: 1px solid #ccc; padding: 8px; text-align: right;">المنتج</th>
                        <th style="border: 1px solid #ccc; padding: 8px; text-align: center;">الكمية</th>
                        <th style="border: 1px solid #ccc; padding: 8px; text-align: left;">الإجمالي</th>
                    </tr>
                </thead>
                <tbody>${rows}</tbody>
            </table>
            <hr style="margin: 16px 0;">
            <p style="display:flex; justify-content: space-between;"><span>مجموع الأصناف</span><span>${subtotal.toFixed(2)} ج.م</span></p>
            <p style="display:flex; justify-content: space-between;"><span>الخصم</span><span>${discount.toFixed(2)} ج.م</span></p>
            ${isDelivery ? `<p style="display:flex; justify-content: space-between;"><span>رسوم التوصيل</span><span>${deliveryFee.toFixed(2)} ج.م</span></p>` : ''}
            <p style="text-align: center; font-size: 18px; font-weight: bold; margin-top: 12px;">الإجمالي المحصل: ${Number(order.total ?? 0).toFixed(2)} ج.م</p>
        </div>`;

            openPrintWindow(body, `فاتورة ${order.invoice_number ?? ''}`);
        });

        Livewire.on('print-daily-report', (payload) => {
            const data = payload ?? {};
            const scopedNote = data.scoped_to_shift ? '<p style="text-align:center;color:#555;margin-bottom:16px;">(طلبات الوردية الحالية فقط)</p>' : '';
            let rowsHtml = '';
            (data.orders || []).forEach((order) => {
                rowsHtml += `
            <tr>
                <td style="border: 1px solid #ccc; padding: 10px;">${escapeHtml(order.invoice_number)}</td>
                <td style="border: 1px solid #ccc; padding: 10px;">${escapeHtml(new Date(order.created_at).toLocaleString('ar-EG'))}</td>
                <td style="border: 1px solid #ccc; padding: 10px; text-align: right;">${Number(order.total ?? 0).toFixed(2)} جنيه مصري</td>
            </tr>`;
            });

            const body = `
        <div style="font-family: Tahoma, Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 12px;" dir="rtl">
            <h1 style="text-align: center; margin-bottom: 12px;">التقرير اليومي — ${escapeHtml(data.date)}</h1>
            ${scopedNote}
            <div style="display: flex; justify-content: space-between; margin-bottom: 20px; flex-wrap: wrap; gap: 8px;">
                <p><strong>إجمالي المبيعات:</strong> ${Number(data.totalSales ?? 0).toFixed(2)} ج.م</p>
                <p><strong>عدد الطلبات:</strong> ${Number(data.totalOrders ?? 0)}</p>
            </div>
            <table style="width: 100%; border-collapse: collapse; margin-top: 12px;">
                <thead>
                    <tr>
                        <th style="border: 1px solid #ccc; padding: 10px; text-align: right;">رقم الفاتورة</th>
                        <th style="border: 1px solid #ccc; padding: 10px; text-align: right;">التاريخ</th>
                        <th style="border: 1px solid #ccc; padding: 10px; text-align: left;">الإجمالي</th>
                    </tr>
                </thead>
                <tbody>${rowsHtml}</tbody>
            </table>
        </div>`;

            openPrintWindow(body, `تقرير ${data.date ?? ''}`);
        });
    }
</script>
@endscript