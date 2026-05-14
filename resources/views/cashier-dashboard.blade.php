<x-app-layout>
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900 py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-2">لوحة تحكم الكاشير 💰</h1>
                    <p class="text-gray-600 dark:text-gray-400">إدارة الطلبات والمبيعات اليومية</p>
                </div>

                @php
                    $activeShift = \App\Models\Shift::where('user_id', auth()->id())
                        ->whereNull('closed_at')
                        ->first();
                @endphp

                @if(!$activeShift)
                <!-- فتح وردية -->
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-6 mb-8">
                    <div class="flex items-center mb-4">
                        <span class="text-3xl mr-4">⏰</span>
                        <div>
                            <h3 class="text-xl font-semibold text-yellow-800 dark:text-yellow-200">فتح وردية العمل</h3>
                            <p class="text-yellow-600 dark:text-yellow-400">يجب فتح وردية قبل البدء في العمل</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('shifts.open') }}" class="flex items-center gap-4">
                        @csrf
                        <input type="number" name="opening_balance" step="0.01" placeholder="الرصيد الافتتاحي"
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                        <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-lg font-medium transition">
                            فتح الوردية 🏁
                        </button>
                    </form>
                </div>
                @else
                <!-- الوردية مفتوحة -->
                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-6 mb-8">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <span class="text-3xl mr-4">✅</span>
                            <div>
                                <h3 class="text-xl font-semibold text-green-800 dark:text-green-200">الوردية مفتوحة</h3>
                                <p class="text-green-600 dark:text-green-400">
                                    بدأت في: {{ optional($activeShift->opened_at ?? $activeShift->created_at)->format('H:i d/m/Y') }}
                                </p>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('shifts.close', $activeShift) }}" class="inline-flex flex-wrap items-center gap-3">
                            @csrf
                            @method('PATCH')
                            <input type="number" name="actual_cash" step="0.01" min="0" placeholder="المبلغ الفعلي في الدراجة"
                                class="w-full max-w-xs px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg font-medium transition">
                                إغلاق الوردية 🏁
                            </button>
                        </form>
                        @error('actual_cash')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- المهام الرئيسية -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                    <!-- إدارة الطلبات -->
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-xl font-semibold mb-2">إدارة الطلبات</h3>
                                <p class="text-blue-100">عرض وطباعة الفواتير</p>
                            </div>
                            <span class="text-4xl">🧾</span>
                        </div>
                        <a href="{{ route('orders') }}" class="mt-4 inline-block bg-white text-blue-600 px-4 py-2 rounded-lg font-medium hover:bg-blue-50 transition">
                            الطلبات →
                        </a>
                    </div>

                    <!-- شاشة الكاشير -->
                    <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-xl font-semibold mb-2">شاشة البيع</h3>
                                <p class="text-green-100">إنشاء طلبات جديدة</p>
                            </div>
                            <span class="text-4xl">🛒</span>
                        </div>
                        <a href="{{ route('cashier') }}" class="mt-4 inline-block bg-white text-green-600 px-4 py-2 rounded-lg font-medium hover:bg-green-50 transition">
                            البدء في البيع →
                        </a>
                    </div>

                    <!-- المصروفات -->
                    <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-xl font-semibold mb-2">المصروفات</h3>
                                <p class="text-purple-100">تسجيل المصروفات اليومية</p>
                            </div>
                            <span class="text-4xl">💸</span>
                        </div>
                        <a href="{{ route('expenses') }}" class="mt-4 inline-block bg-white text-purple-600 px-4 py-2 rounded-lg font-medium hover:bg-purple-50 transition">
                            إدارة المصروفات →
                        </a>
                    </div>
                </div>

                <!-- إحصائيات الوردية -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">إحصائيات الوردية الحالية</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
                        @php
                            $shiftOrders = \App\Models\Order::where('shift_id', $activeShift->id)->where('status', 'completed')->get();
                            $shiftSales = $shiftOrders->sum('total');
                            $shiftGoodsNet = $shiftOrders->sum(fn ($o) => (float) $o->subtotal - (float) $o->discount);
                            $shiftDeliveryFees = $shiftOrders->sum('delivery_fee');
                            $shiftOrderCount = $shiftOrders->count();
                            $shiftExpenses = \App\Models\Expense::where('shift_id', $activeShift->id)->sum('amount');
                            $expectedCash = $activeShift->opening_cash + $shiftSales - $shiftExpenses;
                        @endphp

                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">{{ number_format($shiftSales, 2) }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">إجمالي المحصل</div>
                        </div>
                        <div class="text-center">
                            <div class="text-xl font-bold text-slate-700 dark:text-slate-200">{{ number_format($shiftGoodsNet, 2) }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">صافي البضاعة</div>
                        </div>
                        <div class="text-center">
                            <div class="text-xl font-bold text-amber-600">{{ number_format($shiftDeliveryFees, 2) }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">رسوم التوصيل</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600">{{ $shiftOrderCount }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">عدد الطلبات</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-red-600">{{ number_format($shiftExpenses, 2) }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">مصروفات الوردية</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-purple-600">{{ number_format($shiftSales - $shiftExpenses, 2) }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">صافي الوردية</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-slate-900 dark:text-slate-100">{{ number_format($expectedCash, 2) }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">المبلغ المتوقع في الدراجة</div>
                        </div>
                    </div>
                </div>
                @endif

                <div class="mt-8 text-center">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-6 py-3 rounded-lg font-medium transition">
                            تسجيل الخروج 🚪
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>