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
            <div class="flex items-center justify-between mb-8">
                <h1 class="text-2xl font-black text-[#0c4a6e]">تقارير اليوم</h1>
                <a href="{{ route('cashier') }}" class="bg-white px-4 py-2 rounded-xl shadow-sm text-sm text-[#0c4a6e] font-bold hover:bg-blue-50 transition">
                    ← العودة للكاشير
                </a>
            </div>

            <!-- Summary Card -->
            <div class="bg-white rounded-[2.5rem] shadow-2xl overflow-hidden border border-white">
                <!-- Sales Header -->
                <div class="bg-[#0c4a6e] p-8 text-white text-center">
                    <p class="opacity-70 text-sm font-bold mb-1">إجمالي مبيعات اليوم</p>
                    <h2 class="text-5xl font-black tracking-tight">
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

                    <!-- Discounts -->
                    <div class="flex items-center justify-between bg-red-50 p-4 rounded-2xl">
                        <div class="flex items-center gap-3">
                            <div class="bg-red-100 p-2 rounded-lg text-2xl text-red-600">✂️</div>
                            <span class="font-bold text-slate-600">إجمالي الخصومات</span>
                        </div>
                        <span class="text-2xl font-black text-red-600">{{ number_format($summary->total_discounts ?? 0, 2) }}</span>
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