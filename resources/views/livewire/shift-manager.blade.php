<div>
    @if($showModal)
        <div class="fixed inset-0 z-50 bg-black/40 flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl shadow-2xl w-full max-w-2xl p-6 relative">
                <button wire:click="closeModal" class="absolute top-4 left-4 text-slate-500 hover:text-slate-900 text-2xl">&times;</button>
                <h2 class="text-2xl font-bold text-slate-800 mb-4">إدارة الوردية</h2>

                @if(session()->has('message'))
                    <div class="mb-4 rounded-xl bg-green-50 border border-green-200 text-green-700 px-4 py-3">
                        {{ session('message') }}
                    </div>
                @endif

                @if($activeShift)
                    <div class="space-y-4">
                        <div class="rounded-2xl bg-slate-50 p-4 border border-slate-200">
                            <p class="text-slate-600">الوردية الحالية مفتوحة.</p>
                            <p class="text-slate-700 font-semibold">تاريخ الفتح: {{ $activeShift->opened_at->format('Y-m-d H:i') }}</p>
                            <p class="text-slate-700 font-semibold">رصيد الافتتاح: {{ number_format($activeShift->opening_cash, 2) }} ج.م</p>
                        </div>

                        <div class="rounded-2xl border border-indigo-200 bg-indigo-50 p-4">
                            <h3 class="mb-3 text-sm font-bold text-indigo-900">ملخص الوردية حتى الآن</h3>
                            <div class="grid grid-cols-1 gap-2 text-sm sm:grid-cols-2">
                                <div class="flex items-center justify-between rounded-xl bg-white px-3 py-2">
                                    <span class="text-slate-600">إجمالي المبيعات</span>
                                    <span class="font-bold text-slate-900">{{ number_format($shiftSummary['total_sales'] ?? 0, 2) }} ج.م</span>
                                </div>
                                <div class="flex items-center justify-between rounded-xl bg-white px-3 py-2">
                                    <span class="text-slate-600">إجمالي المصروفات</span>
                                    <span class="font-bold text-red-600">{{ number_format($shiftSummary['total_expenses'] ?? 0, 2) }} ج.م</span>
                                </div>
                                <div class="flex items-center justify-between rounded-xl bg-white px-3 py-2">
                                    <span class="text-slate-600">صافي الكاش المتوقع</span>
                                    <span class="font-bold text-emerald-600">{{ number_format($shiftSummary['net_cash'] ?? 0, 2) }} ج.م</span>
                                </div>
                                <div class="flex items-center justify-between rounded-xl bg-white px-3 py-2">
                                    <span class="text-slate-600">عدد الطلبات</span>
                                    <span class="font-bold text-slate-900">{{ $shiftSummary['total_orders'] ?? 0 }}</span>
                                </div>
                            </div>
                            <div class="mt-3 grid grid-cols-3 gap-2 text-xs">
                                <div class="rounded-lg bg-white px-2 py-2 text-center">
                                    <p class="text-slate-500">دليفري</p>
                                    <p class="mt-1 font-bold text-indigo-700">{{ $shiftSummary['delivery_orders'] ?? 0 }}</p>
                                </div>
                                <div class="rounded-lg bg-white px-2 py-2 text-center">
                                    <p class="text-slate-500">تيك أواي</p>
                                    <p class="mt-1 font-bold text-indigo-700">{{ $shiftSummary['takeaway_orders'] ?? 0 }}</p>
                                </div>
                                <div class="rounded-lg bg-white px-2 py-2 text-center">
                                    <p class="text-slate-500">داخل المحل</p>
                                    <p class="mt-1 font-bold text-indigo-700">{{ $shiftSummary['store_orders'] ?? 0 }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <label class="block">
                                <span class="text-slate-600">الرصيد الفعلي عند الإغلاق</span>
                                <input type="number" step="0.01" wire:model.defer="actual_cash" class="mt-2 w-full rounded-2xl border border-slate-300 px-4 py-3 focus:border-indigo-500 outline-none" placeholder="0.00">
                                @error('actual_cash')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </label>
                        </div>

                        @php
                            $shiftPrintData = [
                                'opened_at' => optional($activeShift->opened_at)->format('Y-m-d H:i'),
                                'opening_cash' => (float) $activeShift->opening_cash,
                                'total_sales' => (float) ($shiftSummary['total_sales'] ?? 0),
                                'total_expenses' => (float) ($shiftSummary['total_expenses'] ?? 0),
                                'net_cash' => (float) ($shiftSummary['net_cash'] ?? 0),
                                'total_orders' => (int) ($shiftSummary['total_orders'] ?? 0),
                                'delivery_orders' => (int) ($shiftSummary['delivery_orders'] ?? 0),
                                'takeaway_orders' => (int) ($shiftSummary['takeaway_orders'] ?? 0),
                                'store_orders' => (int) ($shiftSummary['store_orders'] ?? 0),
                            ];
                        @endphp

                        <div class="flex flex-col sm:flex-row gap-3 mt-4">
                            <button
                                type="button"
                                onclick='printShiftSummary(@json($shiftPrintData))'
                                class="px-5 py-3 bg-indigo-600 text-white rounded-2xl font-semibold hover:bg-indigo-700 transition"
                            >
                                طباعة ملخص الوردية
                            </button>
                            <button wire:click="closeShift" wire:loading.attr="disabled" class="px-5 py-3 bg-red-500 text-white rounded-2xl font-semibold hover:bg-red-600 transition">إغلاق الوردية</button>
                            <button wire:click="closeModal" class="px-5 py-3 bg-slate-200 text-slate-700 rounded-2xl font-semibold hover:bg-slate-300 transition">إلغاء</button>
                        </div>
                    </div>
                @else
                    <div class="space-y-4">
                        <p class="text-slate-600">لا توجد وردية مفتوحة حالياً.</p>
                        <label class="block">
                            <span class="text-slate-600">رصيد الافتتاح</span>
                            <input type="number" step="0.01" wire:model.defer="opening_cash" class="mt-2 w-full rounded-2xl border border-slate-300 px-4 py-3 focus:border-indigo-500 outline-none" placeholder="0.00">
                            @error('opening_cash')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </label>

                        <div class="flex flex-col sm:flex-row gap-3 mt-4">
                            <button wire:click="openShift" wire:loading.attr="disabled" class="px-5 py-3 bg-indigo-600 text-white rounded-2xl font-semibold hover:bg-indigo-700 transition">فتح وردية جديدة</button>
                            <button wire:click="closeModal" class="px-5 py-3 bg-slate-200 text-slate-700 rounded-2xl font-semibold hover:bg-slate-300 transition">إلغاء</button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>

<script>
    function printShiftSummary(summary) {
        if (!summary) return;

        const w = window.open('', '_blank', 'width=700,height=900');
        if (!w) return;

        const html = `
            <!doctype html>
            <html lang="ar" dir="rtl">
            <head>
                <meta charset="utf-8" />
                <title>ملخص الوردية</title>
                <style>
                    body { font-family: Tahoma, Arial, sans-serif; margin: 24px; color: #111; }
                    h1 { margin: 0 0 8px; }
                    .muted { color: #555; margin-bottom: 18px; }
                    .grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; }
                    .card { border: 1px solid #ddd; border-radius: 8px; padding: 10px; }
                    .label { color: #555; font-size: 13px; }
                    .value { font-weight: 700; margin-top: 4px; }
                    .types { margin-top: 14px; border: 1px dashed #bbb; padding: 10px; border-radius: 8px; }
                    .types div { display: flex; justify-content: space-between; margin: 6px 0; }
                </style>
            </head>
            <body onload="window.print(); setTimeout(() => window.close(), 300);">
                <h1>ملخص إغلاق الوردية</h1>
                <div class="muted">تاريخ فتح الوردية: ${summary.opened_at ?? '-'}</div>
                <div class="grid">
                    <div class="card"><div class="label">رصيد الافتتاح</div><div class="value">${Number(summary.opening_cash ?? 0).toFixed(2)} ج.م</div></div>
                    <div class="card"><div class="label">إجمالي المبيعات</div><div class="value">${Number(summary.total_sales ?? 0).toFixed(2)} ج.م</div></div>
                    <div class="card"><div class="label">إجمالي المصروفات</div><div class="value">${Number(summary.total_expenses ?? 0).toFixed(2)} ج.م</div></div>
                    <div class="card"><div class="label">صافي الكاش المتوقع</div><div class="value">${Number(summary.net_cash ?? 0).toFixed(2)} ج.م</div></div>
                </div>
                <div class="types">
                    <div><span>عدد الطلبات الكلي</span><strong>${summary.total_orders ?? 0}</strong></div>
                    <div><span>طلبات دليفري</span><strong>${summary.delivery_orders ?? 0}</strong></div>
                    <div><span>طلبات تيك أواي</span><strong>${summary.takeaway_orders ?? 0}</strong></div>
                    <div><span>طلبات داخل المحل</span><strong>${summary.store_orders ?? 0}</strong></div>
                </div>
            </body>
            </html>
        `;

        w.document.open();
        w.document.write(html);
        w.document.close();
    }
</script>
