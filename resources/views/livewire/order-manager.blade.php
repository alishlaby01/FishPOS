<div class="p-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">كل الطلبات</h3>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('cashier') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                الرجوع الى الكاشير
            </a>
            <button wire:click="printDailyReport" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                طباعة التقرير اليومي 📊
            </button>
        </div>
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
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">نوع الطلب</label>
                <select wire:model.live="typeFilter" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">جميع الأنواع</option>
                    <option value="delivery">دليفري</option>
                    <option value="takeaway">تيك أواي</option>
                    <option value="store">صالة</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">التاريخ</label>
                <input wire:model.live="dateFilter" type="date" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div class="rounded-lg border border-gray-200 bg-white px-4 py-4 text-sm text-gray-700 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200">
            <div class="font-semibold text-gray-900 dark:text-gray-100">إجمالي قيمة الفواتير</div>
            <div class="mt-2 text-xl font-bold text-green-700 dark:text-green-300">{{ number_format($totalSales, 2) }} جنيه مصري</div>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white px-4 py-4 text-sm text-gray-700 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200">
            <div class="font-semibold text-gray-900 dark:text-gray-100">عدد الفواتير</div>
            <div class="mt-2 text-xl font-bold text-slate-800 dark:text-slate-200">{{ $orders->total() }}</div>
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
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 z-50 flex items-start justify-center overflow-y-auto" id="order-modal">
        <div class="relative my-8 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white dark:bg-gray-800 max-h-[90vh] overflow-y-auto">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4 sticky top-0 bg-white dark:bg-gray-800">
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
            const printWindow = window.open('', '_blank', 'width=320,height=700');
            if (! printWindow) {
                window.alert('تعذر فتح نافذة الطباعة. تأكد من السماح بالنوافذ المنبثقة ثم حاول مرة أخرى.');
                return;
            }
            const doc = `<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>${escapeHtml(title)}</title>
    <style>
        @page {
            size: 80mm auto;
            margin: 0;
        }
        html, body {
            margin: 0;
            padding: 0;
            width: 80mm;
            background: #fff;
            font-family: Arial, Tahoma, sans-serif;
            color: #000;
            direction: rtl;
        }
        * {
            box-sizing: border-box;
        }
        .receipt-print {
            width: 72mm;
            min-width: 72mm;
            margin: 0 auto;
            padding: 4mm 4mm 6mm;
            font-size: 12px;
            line-height: 1.5;
            color: #000;
        }
        .receipt-print h2 {
            margin: 0 0 8px;
            text-align: center;
            font-size: 15px;
            font-weight: 700;
            color: #000;
        }
        .receipt-print p {
            margin: 4px 0;
            font-size: 12px;
            line-height: 1.5;
            font-weight: 500;
            color: #000;
        }
        .receipt-print table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
            table-layout: fixed;
            font-size: 12px;
        }
        .receipt-print th,
        .receipt-print td {
            border: 1px solid #000;
            padding: 4px 3px;
            line-height: 1.5;
            word-break: break-word;
            white-space: normal;
            overflow-wrap: anywhere;
            color: #000;
        }
        .receipt-print th {
            background: #fff;
            text-align: right;
            font-weight: 700;
            color: #000;
        }
        .receipt-print td {
            vertical-align: top;
        }
        .receipt-print .item-name {
            width: 45%;
            text-align: right;
            font-weight: 600;
            word-break: break-word;
            white-space: normal;
            overflow-wrap: anywhere;
            color: #000;
        }
        .receipt-print .item-center {
            width: 15%;
            text-align: center;
            font-weight: 600;
            color: #000;
        }
        .receipt-print .item-price,
        .receipt-print .item-right {
            width: 20%;
            text-align: left;
            font-weight: 600;
            color: #000;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin: 4px 0;
            font-size: 13px;
            line-height: 1.5;
            font-weight: 600;
            color: #000;
        }
        .summary-row.total {
            font-weight: 700;
            font-size: 14px;
            border-top: 1px solid #000;
            padding-top: 3px;
            margin-top: 6px;
            color: #000;
        }
        .receipt-print hr {
            border: none;
            border-top: 1px solid #000;
            margin: 5px 0;
        }
    </style>
</head>
<body>
${bodyHtml}
</body>
</html>`;
            printWindow.document.open();
            printWindow.document.write(doc);
            printWindow.document.close();
            printWindow.onload = function() {
                setTimeout(() => {
                    printWindow.print();
                }, 100);
            };
            printWindow.onafterprint = function() {
                setTimeout(() => {
                    printWindow.close();
                }, 1000);
            };
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
                const quantity = Number(item.quantity ?? 0).toFixed(2);
                const unitPrice = Number(item.unit_price ?? 0).toFixed(2);
                const itemTotal = Number(item.total ?? 0).toFixed(2);
                rows += `
            <tr>
                <td class="item-name">${pname}</td>
                <td class="item-center">${quantity}</td>
                <td class="item-price">${unitPrice}</td>
                <td class="item-right">${itemTotal} ج.م</td>
            </tr>`;
            });
            if (isDelivery && deliveryFee > 0) {
                rows += `
            <tr>
                <td class="item-name">رسوم التوصيل</td>
                <td class="item-center">—</td>
                <td class="item-price">—</td>
                <td class="item-right">${deliveryFee.toFixed(2)} ج.م</td>
            </tr>`;
            }

            const creatorName = escapeHtml(order.creator?.name ?? order.creator_name ?? 'غير معروف');
            const inv = escapeHtml(order.invoice_number ?? '');
            const printedAt = new Date().toLocaleString('ar-EG', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false,
            });
            const body = `
        <div class="receipt-print" dir="rtl">
            <h2>فاتورة الطلب</h2>
            <p><strong>رقم الفاتورة:</strong> ${inv}</p>
            <p><strong>التاريخ:</strong> ${escapeHtml(printedAt)}</p>
            <p><strong>الكاشير:</strong> ${creatorName}</p>
            <hr>
            <table>
                <thead>
                    <tr>
                        <th class="item-name">المنتج</th>
                        <th class="item-center">الكم</th>
                        <th class="item-price">السعر</th>
                        <th class="item-right">الإجمالي</th>
                    </tr>
                </thead>
                <tbody>${rows}</tbody>
            </table>
            <hr>
            <div class="summary-row"><span>الأصناف</span><span>${subtotal.toFixed(2)} ج.م</span></div>
            <div class="summary-row"><span>الخصم</span><span>${discount.toFixed(2)} ج.م</span></div>
            ${isDelivery ? `<div class="summary-row"><span>التوصيل</span><span>${deliveryFee.toFixed(2)} ج.م</span></div>` : ''}
            <div class="summary-row total">الإجمالي: ${Number(order.total ?? 0).toFixed(2)} ج.م</div>
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