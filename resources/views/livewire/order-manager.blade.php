<div class="p-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">إدارة الطلبات</h3>
        <button wire:click="printDailyReport" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            طباعة التقرير اليومي 📊
        </button>
    </div>

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
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                        {{ number_format($order->total, 2) }} جنيه مصري
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                        {{ $order->creator->name ?? 'غير معروف' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <button wire:click="printOrder({{ $order->id }})" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 mr-3">
                            🖨️ طباعة
                        </button>
                        <button wire:click="viewOrder({{ $order->id }})" class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-300">
                            👁️ عرض
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                        لا توجد طلبات
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal لعرض تفاصيل الطلب -->
    @if(isset($selectedOrder))
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" id="order-modal">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">تفاصيل الطلب: {{ $selectedOrder->invoice_number }}</h3>
                    <button wire:click="closeOrderModal" class="text-gray-400 hover:text-gray-600">
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
                            <strong>الإجمالي:</strong> {{ number_format($selectedOrder->total, 2) }} جنيه مصري
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
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<script>
$wire.on('print-order', (data) => {
    // طباعة الطلب كـ PDF
    const order = data.order;
    let content = `
        <div style="font-family: Arial, sans-serif; max-width: 300px; margin: 0 auto; padding: 20px; background: white; color: black;">
            <h2 style="text-align: center; margin-bottom: 20px;">فاتورة الطلب</h2>
            <p><strong>رقم الفاتورة:</strong> ${order.invoice_number}</p>
            <p><strong>التاريخ:</strong> ${new Date(order.created_at).toLocaleString('ar-EG')}</p>
            <p><strong>الكاشير:</strong> ${order.creator?.name || 'غير معروف'}</p>
            <hr style="margin: 20px 0;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th style="border: 1px solid #ccc; padding: 8px; text-align: left;">المنتج</th>
                        <th style="border: 1px solid #ccc; padding: 8px; text-align: center;">الكمية</th>
                        <th style="border: 1px solid #ccc; padding: 8px; text-align: right;">الإجمالي</th>
                    </tr>
                </thead>
                <tbody>
    `;

    order.order_items.forEach(item => {
        content += `
            <tr>
                <td style="border: 1px solid #ccc; padding: 8px;">${item.product.name}</td>
                <td style="border: 1px solid #ccc; padding: 8px; text-align: center;">${item.quantity}</td>
                <td style="border: 1px solid #ccc; padding: 8px; text-align: right;">${item.total.toFixed(2)} ريال</td>
            </tr>
        `;
    });

    content += `
                </tbody>
            </table>
            <hr style="margin: 20px 0;">
            <p style="text-align: right; font-size: 18px; font-weight: bold;">الإجمالي: ${order.total.toFixed(2)} جنيه مصري</p>
        </div>
    `;

    const printWindow = window.open('', '_blank');
    printWindow.document.write(content);
    printWindow.document.close();
    printWindow.print();
});

$wire.on('print-daily-report', (data) => {
    let content = `
        <div style="font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; background: white; color: black;">
            <h1 style="text-align: center; margin-bottom: 30px;">التقرير اليومي - ${data.date}</h1>
            <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
                <p><strong>إجمالي المبيعات:</strong> ${data.totalSales.toFixed(2)} جنيه مصري</p>
                <p><strong>عدد الطلبات:</strong> ${data.totalOrders}</p>
            </div>
            <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
                <thead>
                    <tr>
                        <th style="border: 1px solid #ccc; padding: 10px; text-align: left;">رقم الفاتورة</th>
                        <th style="border: 1px solid #ccc; padding: 10px; text-align: left;">التاريخ</th>
                        <th style="border: 1px solid #ccc; padding: 10px; text-align: right;">الإجمالي</th>
                    </tr>
                </thead>
                <tbody>
    `;

    data.orders.forEach(order => {
        content += `
            <tr>
                <td style="border: 1px solid #ccc; padding: 10px;">${order.invoice_number}</td>
                <td style="border: 1px solid #ccc; padding: 10px;">${new Date(order.created_at).toLocaleString('ar-EG')}</td>
                <td style="border: 1px solid #ccc; padding: 10px; text-align: right;">${order.total.toFixed(2)} جنيه مصري</td>
            </tr>
        `;
    });

    content += `
                </tbody>
            </table>
        </div>
    `;

    const printWindow = window.open('', '_blank');
    printWindow.document.write(content);
    printWindow.document.close();
    printWindow.print();
});
</script>