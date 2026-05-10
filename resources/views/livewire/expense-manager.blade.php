<div class="min-h-screen bg-[#F4F7FE] font-sans p-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-[#1E1B4B]">إدارة المصاريف</h1>
                <p class="text-slate-500 mt-2">إضافة وتتبع المصاريف اليومية</p>
            </div>
            <div class="flex gap-4">
                <a href="{{ route('cashier') }}" class="bg-slate-600 hover:bg-slate-700 text-white px-6 py-3 rounded-2xl font-bold flex items-center gap-2 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                    </svg>
                    العودة للكاشير
                </a>
                <button wire:click="openModal" class="bg-[#5B45FF] hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl font-bold flex items-center gap-2 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    إضافة مصروف جديد
                </button>
            </div>
        </div>

        <!-- Search -->
        <div class="mb-6">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="البحث في المصاريف..."
                class="w-full max-w-md bg-white border border-slate-300 rounded-2xl px-4 py-3 focus:border-indigo-500 outline-none">
        </div>

        <div class="mb-6 rounded-3xl border border-indigo-100 bg-white p-5 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="text-lg font-bold text-indigo-900">
                        @if(isset($activeShift) && $activeShift)
                            مصروفات الوردية الحالية
                        @else
                            تقرير مصروفات اليوم (كل الورديات)
                        @endif
                    </h2>
                    <p class="text-sm text-slate-500">
                        @if(isset($activeShift) && $activeShift)
                            وردية مفتوحة منذ {{ optional($activeShift->opened_at)->format('Y-m-d H:i') }}
                        @else
                            تاريخ اليوم: {{ now()->format('Y-m-d') }}
                        @endif
                    </p>
                </div>
                <button onclick="window.print()" class="rounded-xl bg-orange-600 px-4 py-2 text-sm font-bold text-white hover:bg-orange-700">
                    طباعة التقرير اليومي
                </button>
            </div>
            <div class="mt-4 rounded-2xl bg-orange-50 p-4 text-orange-700">
                @if(isset($activeShift) && $activeShift)
                    إجمالي مصروفات هذه الوردية: <span class="font-black">{{ number_format($todayTotalExpenses ?? 0, 2) }} ج.م</span>
                @else
                    إجمالي مصروفات اليوم: <span class="font-black">{{ number_format($todayTotalExpenses ?? 0, 2) }} ج.م</span>
                @endif
            </div>
            <div class="mt-3 space-y-2">
                @forelse($todayExpenses as $expense)
                    <div class="flex items-center justify-between rounded-xl border border-slate-200 px-3 py-2 text-sm">
                        <span class="font-semibold text-slate-700">{{ $expense->title }}</span>
                        <span class="text-slate-500">{{ $expense->created_at->format('H:i') }}</span>
                        <span class="font-bold text-red-600">{{ number_format($expense->amount, 2) }} ج.م</span>
                    </div>
                @empty
                    <div class="rounded-xl border border-dashed border-slate-300 px-3 py-5 text-center text-sm text-slate-500">
                        @if(isset($activeShift) && $activeShift)
                            لا توجد مصروفات مسجلة في هذه الوردية بعد.
                        @else
                            لا توجد مصروفات مسجلة اليوم.
                        @endif
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Success Message -->
        @if(session()->has('message'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-2xl">
                {{ session('message') }}
            </div>
        @endif

        <!-- Expenses Table -->
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-4 text-right text-sm font-bold text-slate-600">العنوان</th>
                            <th class="px-6 py-4 text-right text-sm font-bold text-slate-600">المبلغ</th>
                            <th class="px-6 py-4 text-right text-sm font-bold text-slate-600">التاريخ</th>
                            <th class="px-6 py-4 text-right text-sm font-bold text-slate-600">الوردية</th>
                            <th class="px-6 py-4 text-right text-sm font-bold text-slate-600">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($expenses as $expense)
                            <tr class="hover:bg-slate-50">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-800">{{ $expense->title }}</div>
                                    @if($expense->notes)
                                        <div class="text-sm text-slate-500 mt-1">{{ Str::limit($expense->notes, 50) }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <span class="font-bold text-red-600">{{ number_format($expense->amount, 2) }} ج.م</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-slate-600">{{ $expense->created_at->format('Y-m-d H:i') }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($expense->shift)
                                        <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm">
                                            وردية {{ $expense->shift->id }}
                                        </span>
                                    @else
                                        <span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-sm">
                                            غير مرتبط
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex gap-2">
                                        <button wire:click="openModal({{ $expense->id }})"
                                            class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded-lg text-sm transition">
                                            تعديل
                                        </button>
                                        <button wire:click="deleteExpense({{ $expense->id }})"
                                            onclick="return confirm('هل أنت متأكد من حذف هذا المصروف؟')"
                                            class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-lg text-sm transition">
                                            حذف
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                                    لا توجد مصاريف
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 bg-slate-50 border-t">
                {{ $expenses->links() }}
            </div>
        </div>
    </div>

    <!-- Modal -->
    @if($showModal)
        <div class="fixed inset-0 z-50 bg-black/40 flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md p-6 relative">
                <button wire:click="closeModal" class="absolute top-4 left-4 text-slate-500 hover:text-slate-900 text-2xl">&times;</button>
                <h2 class="text-2xl font-bold text-slate-800 mb-4">
                    {{ $editingExpense ? 'تعديل المصروف' : 'إضافة مصروف جديد' }}
                </h2>

                <form wire:submit.prevent="saveExpense" class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-600 mb-2">عنوان المصروف</label>
                        <input type="text" wire:model="title" class="w-full border border-slate-300 rounded-2xl px-4 py-3 focus:border-indigo-500 outline-none" placeholder="مثال: شراء مواد تنظيف">
                        @error('title') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-600 mb-2">المبلغ</label>
                        <input type="number" step="0.01" wire:model="amount" class="w-full border border-slate-300 rounded-2xl px-4 py-3 focus:border-indigo-500 outline-none" placeholder="0.00">
                        @error('amount') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-600 mb-2">ملاحظات</label>
                        <textarea wire:model="notes" rows="3" class="w-full border border-slate-300 rounded-2xl px-4 py-3 focus:border-indigo-500 outline-none" placeholder="تفاصيل إضافية..."></textarea>
                        @error('notes') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>

                    @error('shift')
                        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-2xl">
                            {{ $message }}
                        </div>
                    @enderror

                    <div class="flex gap-3 mt-6">
                        <button type="submit" wire:loading.attr="disabled"
                            class="flex-1 bg-[#5B45FF] hover:bg-indigo-700 text-white py-3 rounded-2xl font-bold transition">
                            {{ $editingExpense ? 'تحديث' : 'إضافة' }}
                        </button>
                        <button type="button" wire:click="closeModal"
                            class="flex-1 bg-slate-200 hover:bg-slate-300 text-slate-700 py-3 rounded-2xl font-bold transition">
                            إلغاء
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

<style>
    @media print {
        button,
        a,
        .fixed,
        .pagination {
            display: none !important;
        }
        body {
            background: white !important;
        }
    }
</style>