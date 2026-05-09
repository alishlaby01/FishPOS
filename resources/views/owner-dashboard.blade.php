<x-app-layout>
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900 py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-2">لوحة تحكم المالك 👨‍🍳</h1>
                    <p class="text-gray-600 dark:text-gray-400">إدارة المنتجات والمخزون اليومي</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                    <!-- إدارة المنتجات -->
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-xl font-semibold mb-2">إدارة المنتجات</h3>
                                <p class="text-blue-100">إضافة وتعديل المنتجات والأسعار</p>
                            </div>
                            <span class="text-4xl">📦</span>
                        </div>
                        <a href="{{ route('products') }}" class="mt-4 inline-block bg-white text-blue-600 px-4 py-2 rounded-lg font-medium hover:bg-blue-50 transition">
                            إدارة المنتجات →
                        </a>
                    </div>

                    <!-- إدخال المخزون الصباحي -->
                    <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-xl font-semibold mb-2">المخزون الصباحي</h3>
                                <p class="text-green-100">إدخال الكميات الجديدة يومياً</p>
                            </div>
                            <span class="text-4xl">📈</span>
                        </div>
                        <a href="{{ route('morning-stock') }}" class="mt-4 inline-block bg-white text-green-600 px-4 py-2 rounded-lg font-medium hover:bg-green-50 transition">
                            إدخال المخزون →
                        </a>
                    </div>

                    <!-- التقارير -->
                    <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-xl font-semibold mb-2">التقارير</h3>
                                <p class="text-purple-100">مراجعة المبيعات والأرباح</p>
                            </div>
                            <span class="text-4xl">📊</span>
                        </div>
                        <a href="{{ route('summary') }}" class="mt-4 inline-block bg-white text-purple-600 px-4 py-2 rounded-lg font-medium hover:bg-purple-50 transition">
                            عرض التقارير →
                        </a>
                    </div>
                </div>

                <!-- إحصائيات سريعة -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">إحصائيات اليوم</h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        @php
                            $todaySales = \App\Models\Order::whereDate('created_at', today())->sum('total');
                            $todayOrders = \App\Models\Order::whereDate('created_at', today())->count();
                            $totalProducts = \App\Models\Product::where('active', true)->count();
                            $lowStockProducts = \App\Models\Product::where('active', true)->whereColumn('current_stock', '<=', 'min_stock')->count();
                        @endphp

                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">{{ number_format($todaySales, 2) }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">مبيعات اليوم (جنيه مصري)</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600">{{ $todayOrders }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">عدد الطلبات</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-purple-600">{{ $totalProducts }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">إجمالي المنتجات</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-red-600">{{ $lowStockProducts }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">منتجات قليلة المخزون</div>
                        </div>
                    </div>
                </div>

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