<div>
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4" dir="rtl">
            <div class="relative w-full max-w-2xl rounded-3xl border border-slate-200 bg-white p-6 shadow-2xl">
                <button type="button" wire:click="closeModal" class="absolute left-4 top-4 text-2xl leading-none text-slate-400 transition hover:text-slate-700" aria-label="إغلاق">&times;</button>

                <header class="mb-6 border-b border-slate-100 pb-4 pr-8">
                    <h2 class="text-2xl font-bold text-slate-900">إدارة الوردية</h2>
                    <p class="mt-1 text-sm text-slate-500">
                        @if($activeShift)
                            راجع ملخص المبيعات أو أغلق الوردية بعد عد النقدية في الدرج.
                        @else
                            لا توجد وردية مفتوحة. أدخل رصيد الافتتاح للبدء في تسجيل المبيعات.
                        @endif
                    </p>
                </header>

                @if(session()->has('message'))
                    <div class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                        {{ session('message') }}
                    </div>
                @endif

                @if($activeShift)
                    <div class="space-y-4">
                        <div class="rounded-2xl border border-slate-200 bg-gradient-to-br from-slate-50 to-white p-5 shadow-sm">
                            <p class="text-sm font-semibold text-slate-800">وردية نشطة</p>
                            <dl class="mt-3 grid gap-2 text-sm sm:grid-cols-2">
                                <div class="flex justify-between gap-2 rounded-xl bg-white/90 px-3 py-2.5 ring-1 ring-slate-100">
                                    <dt class="text-slate-500">تاريخ الفتح</dt>
                                    <dd class="font-bold text-slate-900">{{ $activeShift->opened_at->format('Y-m-d H:i') }}</dd>
                                </div>
                                <div class="flex justify-between gap-2 rounded-xl bg-white/90 px-3 py-2.5 ring-1 ring-slate-100">
                                    <dt class="text-slate-500">رصيد الافتتاح</dt>
                                    <dd class="font-bold text-slate-900">{{ number_format($activeShift->opening_cash, 2) }} ج.م</dd>
                                </div>
                            </dl>
                        </div>

                        <div class="rounded-2xl border border-indigo-200 bg-indigo-50 p-5 shadow-sm">
                            <h3 class="mb-3 text-sm font-bold text-indigo-900">ملخص الوردية حتى الآن</h3>
                            <div class="grid grid-cols-1 gap-2 text-sm sm:grid-cols-2">
                                <div class="flex items-center justify-between rounded-xl bg-white px-3 py-2 ring-1 ring-indigo-100">
                                    <span class="text-slate-600">صافي البضاعة <span class="text-xs font-normal text-slate-400">(بعد الخصم)</span></span>
                                    <span class="font-bold text-slate-900">{{ number_format($shiftSummary['goods_net_sales'] ?? 0, 2) }} ج.م</span>
                                </div>
                                <div class="flex items-center justify-between rounded-xl bg-white px-3 py-2 ring-1 ring-indigo-100">
                                    <span class="text-slate-600">رسوم التوصيل <span class="text-xs font-normal text-slate-400">(بند منفصل)</span></span>
                                    <span class="font-bold text-amber-700">{{ number_format($shiftSummary['delivery_fees_total'] ?? 0, 2) }} ج.م</span>
                                </div>
                                <div class="flex items-center justify-between rounded-xl bg-white px-3 py-2 ring-1 ring-indigo-100">
                                    <span class="text-slate-600">إجمالي المحصل</span>
                                    <span class="font-bold text-slate-900">{{ number_format($shiftSummary['total_sales'] ?? 0, 2) }} ج.م</span>
                                </div>
                                <div class="flex items-center justify-between rounded-xl bg-white px-3 py-2 ring-1 ring-indigo-100">
                                    <span class="text-slate-600">إجمالي المصروفات</span>
                                    <span class="font-bold text-red-600">{{ number_format($shiftSummary['total_expenses'] ?? 0, 2) }} ج.م</span>
                                </div>
                                <div class="flex items-center justify-between rounded-xl bg-white px-3 py-2 ring-1 ring-indigo-100">
                                    <span class="text-slate-600">صافي الكاش المتوقع</span>
                                    <span class="font-bold text-emerald-600">{{ number_format($shiftSummary['net_cash'] ?? 0, 2) }} ج.م</span>
                                </div>
                                <div class="flex items-center justify-between rounded-xl bg-white px-3 py-2 ring-1 ring-indigo-100">
                                    <span class="text-slate-600">عدد الطلبات</span>
                                    <span class="font-bold text-slate-900">{{ $shiftSummary['total_orders'] ?? 0 }}</span>
                                </div>
                            </div>
                            <div class="mt-3 grid grid-cols-3 gap-2 text-xs">
                                <div class="rounded-xl bg-white px-2 py-2 text-center ring-1 ring-indigo-100">
                                    <p class="text-slate-500">دليفري</p>
                                    <p class="mt-1 font-bold text-indigo-700">{{ $shiftSummary['delivery_orders'] ?? 0 }}</p>
                                </div>
                                <div class="rounded-xl bg-white px-2 py-2 text-center ring-1 ring-indigo-100">
                                    <p class="text-slate-500">تيك أواي</p>
                                    <p class="mt-1 font-bold text-indigo-700">{{ $shiftSummary['takeaway_orders'] ?? 0 }}</p>
                                </div>
                                <div class="rounded-xl bg-white px-2 py-2 text-center ring-1 ring-indigo-100">
                                    <p class="text-slate-500">داخل المحل</p>
                                    <p class="mt-1 font-bold text-indigo-700">{{ $shiftSummary['store_orders'] ?? 0 }}</p>
                                </div>
                            </div>
                        </div>

                        <label class="block">
                            <span class="text-sm font-semibold text-slate-700">الرصيد الفعلي عند الإغلاق</span>
                            <input type="number" step="0.01" wire:model.defer="actual_cash" class="mt-2 w-full rounded-2xl border border-slate-300 px-4 py-3 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20" placeholder="0.00">
                            @error('actual_cash')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </label>

                        @php
                            $shiftPrintData = [
                                'opened_at' => optional($activeShift->opened_at)->format('Y-m-d H:i'),
                                'opening_cash' => (float) $activeShift->opening_cash,
                                'goods_net_sales' => (float) ($shiftSummary['goods_net_sales'] ?? 0),
                                'delivery_fees_total' => (float) ($shiftSummary['delivery_fees_total'] ?? 0),
                                'total_sales' => (float) ($shiftSummary['total_sales'] ?? 0),
                                'total_expenses' => (float) ($shiftSummary['total_expenses'] ?? 0),
                                'net_cash' => (float) ($shiftSummary['net_cash'] ?? 0),
                                'total_orders' => (int) ($shiftSummary['total_orders'] ?? 0),
                                'delivery_orders' => (int) ($shiftSummary['delivery_orders'] ?? 0),
                                'takeaway_orders' => (int) ($shiftSummary['takeaway_orders'] ?? 0),
                                'store_orders' => (int) ($shiftSummary['store_orders'] ?? 0),
                            ];
                        @endphp

                        <div class="mt-2 flex flex-col gap-3 border-t border-slate-100 pt-6 sm:flex-row sm:flex-wrap">
                            <button
                                type="button"
                                onclick='printShiftSummary(@json($shiftPrintData))'
                                class="inline-flex flex-1 min-w-[140px] justify-center rounded-2xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700"
                            >
                                طباعة ملخص الوردية
                            </button>
                            <button type="button" wire:click="closeShift" wire:loading.attr="disabled" class="inline-flex flex-1 min-w-[140px] justify-center rounded-2xl bg-red-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-red-500 disabled:opacity-50">
                                إغلاق الوردية
                            </button>
                            <button type="button" wire:click="closeModal" class="inline-flex flex-1 min-w-[140px] justify-center rounded-2xl bg-slate-100 px-5 py-3 text-sm font-semibold text-slate-800 ring-1 ring-slate-200 transition hover:bg-slate-200">
                                إلغاء
                            </button>
                        </div>
                    </div>
                @else
                    <div class="space-y-4">
                        <div class="rounded-2xl border border-slate-200 bg-gradient-to-br from-slate-50 to-white p-5 shadow-sm">
                            <p class="text-sm font-semibold text-slate-800">جاهز لفتح وردية جديدة</p>
                            <p class="mt-1 text-sm text-slate-600">أدخل المبلغ الموجود في الدرج عند بداية الشفت.</p>
                        </div>

                        <label class="block">
                            <span class="text-sm font-semibold text-slate-700">رصيد الافتتاح</span>
                            <input type="number" step="0.01" wire:model.defer="opening_cash" class="mt-2 w-full rounded-2xl border border-slate-300 px-4 py-3 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20" placeholder="0.00">
                            @error('opening_cash')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </label>

                        <div class="mt-2 flex flex-col gap-3 border-t border-slate-100 pt-6 sm:flex-row sm:flex-wrap">
                            <button type="button" wire:click="openShift" wire:loading.attr="disabled" class="inline-flex flex-1 min-w-[140px] justify-center rounded-2xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 disabled:opacity-50">
                                فتح وردية جديدة
                            </button>
                            <button type="button" wire:click="closeModal" class="inline-flex flex-1 min-w-[140px] justify-center rounded-2xl bg-slate-100 px-5 py-3 text-sm font-semibold text-slate-800 ring-1 ring-slate-200 transition hover:bg-slate-200">
                                إلغاء
                            </button>
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
                    <div class="card"><div class="label">صافي البضاعة (بعد الخصم)</div><div class="value">${Number(summary.goods_net_sales ?? 0).toFixed(2)} ج.م</div></div>
                    <div class="card"><div class="label">رسوم التوصيل</div><div class="value">${Number(summary.delivery_fees_total ?? 0).toFixed(2)} ج.م</div></div>
                    <div class="card"><div class="label">إجمالي المحصل</div><div class="value">${Number(summary.total_sales ?? 0).toFixed(2)} ج.م</div></div>
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
