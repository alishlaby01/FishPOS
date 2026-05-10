{{-- resources/views/livewire/stock/morning-entry.blade.php --}}
<div class="min-h-screen bg-[#F4F7FE] font-sans p-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-[#1E1B4B]">إدارة المخزون</h1>
                <p class="text-slate-500 mt-2">إدخال المخزون الصباحي وتسجيل الهالك</p>
            </div>
            <div class="flex gap-4">
                <a href="{{ route('cashier') }}" class="bg-slate-600 hover:bg-slate-700 text-white px-6 py-3 rounded-2xl font-bold flex items-center gap-2 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                    </svg>
                    العودة للكاشير
                </a>
            </div>
        </div>

        <!-- Success Message -->
        @if(session()->has('message'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-2xl">
                {{ session('message') }}
            </div>
        @endif

        @if(session()->has('error'))
            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-2xl">
                {{ session('error') }}
            </div>
        @endif

        @if(isset($productsWithoutStock) && $productsWithoutStock->count() > 0)
            <div class="mb-6 bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-4 rounded-2xl">
                <div class="font-semibold">هناك {{ $productsWithoutStock->count() }} منتج بمخزون صفري أو غير محدد (حسب عمود المخزون).</div>
                <div class="text-sm text-slate-600">يرجى مراجعة الكميات وتسجيل التوريد أو الهالك حسب الحاجة.</div>
            </div>
        @endif

        <!-- تبويبات -->
        <div class="flex mb-6 bg-slate-100 rounded-full p-1">
            <button wire:click="$set('activeTab', 'supply')"
                    class="flex-1 py-3 px-6 rounded-full font-bold transition-all {{ $activeTab == 'supply' ? 'bg-[#5B45FF] text-white' : 'text-slate-600 hover:text-slate-800' }}">
                توريد صباحي
            </button>
            <button wire:click="$set('activeTab', 'waste')"
                    class="flex-1 py-3 px-6 rounded-full font-bold transition-all {{ $activeTab == 'waste' ? 'bg-[#5B45FF] text-white' : 'text-slate-600 hover:text-slate-800' }}">
                تسجيل هالك
            </button>
        </div>

        @if($activeTab == 'supply')
        <!-- قسم التوريد -->
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden">
            <div class="overflow-x-auto overflow-y-auto max-h-[65vh]">
                <table class="w-full table-fixed">
                    <thead class="bg-slate-50 sticky top-0 z-10">
                        <tr>
                            <th class="px-6 py-4 text-right text-sm font-bold text-slate-600">المنتج</th>
                            <th class="px-6 py-4 text-right text-sm font-bold text-slate-600">الكمية الحالية</th>
                            <th class="px-6 py-4 text-center text-sm font-bold text-slate-600">الكمية الجديدة (كيلو)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($products as $product)
                            <tr class="hover:bg-slate-50">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-800 flex items-center gap-2">
                                        {{ $product->name }}
                                        @if((float)($product->getRawOriginal('current_stock') ?? 0) <= 0)
                                            <span class="text-[10px] bg-yellow-100 text-yellow-800 uppercase tracking-[0.12em] px-2 py-1 rounded-full">مخزون منخفض</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-slate-600">{{ number_format((float)($product->getRawOriginal('current_stock') ?? 0), 2) }} كيلو</span>
                                </td>
                                <td class="px-6 py-4 flex justify-center">
                                    <input type="number" step="0.01" wire:model="quantities.{{ $product->id }}"
                                           class="bg-slate-100 border border-slate-300 rounded-2xl w-32 text-center px-3 py-2 focus:border-indigo-500 outline-none">
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-12 text-center text-slate-500">
                                    لا توجد منتجات
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-8 flex justify-end">
            <button wire:click="save" class="bg-[#5B45FF] hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl font-bold transition">
                حفظ الكميات
            </button>
        </div>
        @else
        <!-- قسم الهالك -->
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden">
            <div class="overflow-x-auto overflow-y-auto max-h-[65vh]">
                <table class="w-full table-fixed">
                    <thead class="bg-slate-50 sticky top-0 z-10">
                        <tr>
                            <th class="px-6 py-4 text-right text-sm font-bold text-slate-600">المنتج</th>
                            <th class="px-6 py-4 text-right text-sm font-bold text-slate-600">الكمية الحالية</th>
                            <th class="px-6 py-4 text-center text-sm font-bold text-slate-600">كمية الهالك (كيلو)</th>
                            <th class="px-6 py-4 text-center text-sm font-bold text-slate-600">السبب</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($products as $product)
                            <tr class="hover:bg-slate-50">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-800 flex items-center gap-2">
                                        {{ $product->name }}
                                        @if((float)($product->getRawOriginal('current_stock') ?? 0) <= 0)
                                            <span class="text-[10px] bg-yellow-100 text-yellow-800 uppercase tracking-[0.12em] px-2 py-1 rounded-full">مخزون منخفض</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-slate-600">{{ number_format((float)($product->getRawOriginal('current_stock') ?? 0), 2) }} كيلو</span>
                                </td>
                                <td class="px-6 py-4 flex justify-center">
                                    <input type="number" step="0.01" wire:model="wasteQuantities.{{ $product->id }}"
                                           class="bg-slate-100 border border-slate-300 rounded-2xl w-32 text-center px-3 py-2 focus:border-indigo-500 outline-none">
                                </td>
                                <td class="px-6 py-4 flex justify-center">
                                    <select wire:model="wasteReasons.{{ $product->id }}"
                                            class="bg-slate-100 border border-slate-300 rounded-2xl px-3 py-2 focus:border-indigo-500 outline-none">
                                        <option value="">اختر السبب</option>
                                        <option value="تلف">تلف</option>
                                        <option value="تنظيف">تنظيف</option>
                                        <option value="انتهاء الصلاحية">انتهاء الصلاحية</option>
                                        <option value="أخرى">أخرى</option>
                                    </select>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-slate-500">
                                    لا توجد منتجات
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-8 flex justify-end">
            <button wire:click="saveWaste" class="bg-red-500 hover:bg-red-600 text-white px-6 py-3 rounded-2xl font-bold transition">
                تسجيل الهالك
            </button>
        </div>
        @endif
    </div>
</div>