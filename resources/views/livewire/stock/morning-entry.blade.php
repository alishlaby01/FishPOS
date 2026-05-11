{{-- resources/views/livewire/stock/morning-entry.blade.php --}}
<div class="min-h-full bg-[#F4F7FE] font-sans p-8 pb-28">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-[#1E1B4B]">إدارة المخزون</h1>
                <p class="text-slate-500 mt-2">إدخال المخزون الصباحي (التوريد)</p>
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
                <div class="font-semibold">هناك {{ $productsWithoutStock->count() }} منتج بمخزون صفري أو غير محدد.</div>
                <div class="text-sm text-slate-600">يرجى مراجعة الكميات وتسجيل التوريد الصباحي حسب الحاجة.</div>
            </div>
        @endif

        <!-- التوريد الصباحي -->
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden flex flex-col max-h-[calc(100vh-14rem)] min-h-0">
            <div class="overflow-x-auto overflow-y-auto min-h-0 flex-1">
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
                                <td class="px-6 py-4 align-middle">
                                    <div class="font-bold text-slate-800 flex items-center gap-2">
                                        {{ $product->name }}
                                        @if($product->displayStock() <= 0)
                                            <span class="text-[10px] bg-yellow-100 text-yellow-800 uppercase tracking-[0.12em] px-2 py-1 rounded-full">مخزون منخفض</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 align-middle">
                                    <span class="text-slate-600">{{ number_format($product->displayStock(), 2) }} كيلو</span>
                                </td>
                                <td class="px-6 py-4 align-middle text-center">
                                    <input type="number" step="0.01" wire:model="quantities.{{ $product->id }}"
                                           class="inline-block bg-slate-100 border border-slate-300 rounded-2xl w-32 text-center px-3 py-2 focus:border-indigo-500 outline-none">
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

            <div class="flex-shrink-0 border-t border-slate-200 bg-slate-50 px-6 py-4 flex justify-end gap-3">
                <button type="button" wire:click="save" wire:loading.attr="disabled" wire:target="save"
                    class="bg-[#5B45FF] hover:bg-indigo-700 text-white px-8 py-3 rounded-2xl font-bold transition shadow-sm">
                    <span wire:loading.remove wire:target="save">حفظ الكميات</span>
                    <span wire:loading wire:target="save">جاري الحفظ…</span>
                </button>
            </div>
        </div>
    </div>
</div>
