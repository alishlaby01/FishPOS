<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FishPOS - تقارير اليوم</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-[#f0f9ff] antialiased font-sans">
    <div class="min-h-screen p-4 md:p-8">
        <div class="max-w-md mx-auto">
            <!-- Header -->
            <div class="flex flex-col gap-4 mb-8 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-black text-[#0c4a6e]">تقارير الوردية</h1>
                    <p class="text-sm text-slate-500">عرض المبيعات والمصروفات بحسب التاريخ والوردية</p>
                </div>
                <a href="{{ route('cashier') }}" class="bg-white px-4 py-2 rounded-xl shadow-sm text-sm text-[#0c4a6e] font-bold hover:bg-blue-50 transition">
                    ← العودة للكاشير
                </a>
            </div>

            <form method="GET" class="mb-8 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">التاريخ</label>
                        <input type="date" name="date" value="{{ $date ?? today()->toDateString() }}" class="w-full rounded-xl border border-slate-300 px-4 py-2 text-sm outline-none focus:border-indigo-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">الوردية</label>
                        <select name="shift_id" class="w-full rounded-xl border border-slate-300 px-4 py-2 text-sm outline-none focus:border-indigo-500">
                            <option value="">كل الورديات</option>
                            @foreach($shiftOptions as $option)
                                <option value="{{ $option->id }}" @selected(optional($selectedShift)->id == $option->id)>
                                    {{ $option->user->name ?? 'غير معروف' }} - {{ optional($option->closed_at)->format('H:i') }}
                                    ({{ number_format($option->discrepancy ?? 0, 2) }} ج.م)
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full rounded-2xl bg-[#0c4a6e] px-5 py-3 text-sm font-bold text-white hover:bg-[#0b4a6c] transition">
                            تطبيق الفلتر
                        </button>
                    </div>
                </div>
            </form>

            <!-- Summary Card -->
            <div class="bg-white rounded-[2.5rem] shadow-2xl overflow-hidden border border-white">
                <!-- Sales Header -->
                <div class="bg-[#0c4a6e] p-8 text-white text-center">
                        <p class="opacity-70 text-sm font-bold mb-1">إجمالي المبيعات لـ {{ $reportDate }}</p>
                        {{ number_format($summary->total_sales ?? 0, 2) }}
                        <span class="text-lg font-normal">ج.م</span>
                    </h2>
                </div>

                <!-- Details -->
                <div class="p-8 space-y-6">
                    <!-- Orders Count -->
                    <div class="flex items-center justify-between bg-slate-50 p-4 rounded-2xl">
                        <div class="flex items-center gap-3">
                            <div class="bg-blue-100 p-2 rounded-lg text-2xl text-blue-600">📦</div>
                            <span class="font-bold text-slate-600">عدد الطلبات</span>
                        </div>
                        <span class="text-2xl font-black text-[#0c4a6e]">{{ $summary->total_orders ?? 0 }}</span>
                    </div>

                    <!-- Order Types Breakdown -->
                    <div class="bg-indigo-50 p-4 rounded-2xl space-y-2">
                        <p class="font-bold text-slate-700">تفصيل أنواع الطلبات</p>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-slate-600">طلبات دليفري</span>
                            <span class="font-black text-indigo-700">{{ $summary->delivery_orders_count ?? 0 }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-slate-600">طلبات تيك أواي</span>
                            <span class="font-black text-indigo-700">{{ $summary->takeaway_orders_count ?? 0 }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-slate-600">طلبات داخل المحل</span>
                            <span class="font-black text-indigo-700">{{ $summary->store_orders_count ?? 0 }}</span>
                        </div>
                    </div>

                    <!-- Discounts -->
                    <div class="flex items-center justify-between bg-red-50 p-4 rounded-2xl">
                        <div class="flex items-center gap-3">
                            <div class="bg-red-100 p-2 rounded-lg text-2xl text-red-600">✂️</div>
                            <span class="font-bold text-slate-600">إجمالي الخصومات</span>
                        </div>
                        <span class="text-2xl font-black text-red-600">{{ number_format($summary->total_discounts ?? 0, 2) }}</span>
                    </div>

                    <!-- Expenses -->
                    <div class="flex items-center justify-between bg-orange-50 p-4 rounded-2xl">
                        <div class="flex items-center gap-3">
                            <div class="bg-orange-100 p-2 rounded-lg text-2xl text-orange-600">💰</div>
                            <span class="font-bold text-slate-600">إجمالي المصاريف</span>
                        </div>
                        <span class="text-2xl font-black text-orange-600">{{ number_format($summary->total_expenses ?? 0, 2) }}</span>
                    </div>

                    <!-- Net Profit -->
                    <div class="flex items-center justify-between bg-green-50 p-4 rounded-2xl">
                        <div class="flex items-center gap-3">
                            <div class="bg-green-100 p-2 rounded-lg text-2xl text-green-600">📈</div>
                            <span class="font-bold text-slate-600">صافي الربح</span>
                        </div>
                        <span class="text-2xl font-black text-green-600">{{ number_format($summary->net_profit ?? 0, 2) }}</span>
                    </div>

                    <!-- Print Button -->
                    <div class="pt-4">
                        <button onclick="window.print()" class="w-full bg-[#ea580c] hover:bg-[#c2410c] text-white py-5 rounded-2xl font-black text-lg flex items-center justify-center gap-3 shadow-lg shadow-orange-200 transition-all active:scale-95">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                            طباعة التقرير
                        </button>
                    </div>
                </div>
            </div>

            @if(isset($shiftReports) && $shiftReports->isNotEmpty())
                <div class="mt-8 bg-white rounded-3xl p-6 shadow-xl border border-slate-200">
                    <h2 class="text-xl font-bold text-slate-900 mb-4">تقرير ورديات اليوم</h2>
                    <div class="space-y-3">
                        @foreach($shiftReports as $shift)
                            <div class="rounded-2xl border border-slate-200 p-4 bg-slate-50">
                                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                    <div>
                                        <p class="text-slate-600 text-sm">الكاشير</p>
                                        <p class="text-base font-semibold text-slate-900">{{ $shift->user->name ?? 'غير معروف' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-slate-600 text-sm">وقت الإغلاق</p>
                                        <p class="text-base font-semibold text-slate-900">{{ optional($shift->closed_at)->format('H:i d/m/Y') }}</p>
                                    </div>
                                </div>
                                <div class="mt-4 grid grid-cols-2 gap-4 text-sm text-slate-700">
                                    <div class="rounded-xl bg-white p-3 border border-slate-200">
                                        <p class="text-xs uppercase text-slate-400">المبلغ المتوقع</p>
                                        <p class="text-lg font-bold">{{ number_format($shift->expected_cash ?? 0, 2) }} ج.م</p>
                                    </div>
                                    <div class="rounded-xl bg-white p-3 border border-slate-200">
                                        <p class="text-xs uppercase text-slate-400">المبلغ الفعلي</p>
                                        <p class="text-lg font-bold">{{ number_format($shift->actual_cash ?? 0, 2) }} ج.م</p>
                                    </div>
                                    <div class="rounded-xl bg-white p-3 border border-slate-200">
                                        <p class="text-xs uppercase text-slate-400">العجز/الزيادة</p>
                                        <p class="text-lg font-bold {{ $shift->discrepancy < 0 ? 'text-red-600' : 'text-green-600' }}">{{ number_format($shift->discrepancy ?? 0, 2) }} ج.م</p>
                                    </div>
                                    <div class="rounded-xl bg-white p-3 border border-slate-200">
                                        <p class="text-xs uppercase text-slate-400">إجمالي المبيعات</p>
                                        <p class="text-lg font-bold">{{ number_format($shift->total_sales ?? 0, 2) }} ج.م</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
            
            <p class="text-center mt-8 text-slate-400 text-xs font-bold">FishPOS v1.0 - تقرير يومي تلقائي</p>
        </div>
    </div>

    <!-- Print Styles -->
    <!-- Print Styles -->
    <style>
        @media print {
            body { 
                background-color: white !important; 
            }
            button, a { 
                display: none !important; 
            }
            .shadow-2xl, .shadow-xl { 
                box-shadow: none !important; 
                border: 1px solid #eee !important; 
            }
            .bg-\[\#0c4a6e\] { 
                background-color: white !important; 
                color: black !important; 
                border-bottom: 2px solid black !important; 
            }
            .text-white { 
                color: black !important; 
            }
        }
    </style>
</body>
</html>