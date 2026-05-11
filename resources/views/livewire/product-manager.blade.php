<div class="min-h-screen bg-[#F4F7FE] font-sans p-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-[#1E1B4B]">إدارة المنتجات</h1>
                <p class="text-slate-500 mt-2">إضافة وتعديل وحذف المنتجات والأسعار</p>
            </div>
            <div class="flex gap-4">
                <a href="{{ route('cashier') }}" class="bg-slate-600 hover:bg-slate-700 text-white px-6 py-3 rounded-2xl font-bold flex items-center gap-2 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                    </svg>
                    العودة للكاشير
                </a>
                <button type="button" wire:click="openModal" class="bg-[#5B45FF] hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl font-bold flex items-center gap-2 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    إضافة منتج جديد
                </button>
            </div>
        </div>

        <!-- Search -->
        <div class="mb-6">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="البحث عن منتج..."
                class="w-full max-w-md bg-white border border-slate-300 rounded-2xl px-4 py-3 focus:border-indigo-500 outline-none">
        </div>

        <!-- Success Message -->
        @if(session()->has('message'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-2xl">
                {{ session('message') }}
            </div>
        @endif

        <!-- Products Table -->
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-4 text-right text-sm font-bold text-slate-600">المنتج</th>
                            <th class="px-6 py-4 text-right text-sm font-bold text-slate-600">الفئة</th>
                            <th class="px-6 py-4 text-right text-sm font-bold text-slate-600">المخزون الحالي</th>
                            <th class="px-6 py-4 text-right text-sm font-bold text-slate-600">السعر</th>
                            <th class="px-6 py-4 text-right text-sm font-bold text-slate-600">الحالة</th>
                            <th class="px-6 py-4 text-right text-sm font-bold text-slate-600">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($products as $product)
                            <tr wire:key="product-row-{{ $product->id }}" class="hover:bg-slate-50">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-800">{{ $product->name }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="bg-slate-100 text-slate-700 px-3 py-1 rounded-full text-sm">
                                        {{ $product->category->name }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="font-bold text-green-600">{{ $product->current_stock ?? 0 }} {{ $product->unit ?? 'كيلو' }}</span>
                                    @if(($product->current_stock ?? 0) <= ($product->min_stock ?? 5))
                                        <span class="text-xs bg-red-100 text-red-700 px-2 py-1 rounded-full ml-2">قليل</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <span class="font-bold text-[#5B45FF]">{{ number_format($product->price, 2) }} ج.م</span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($product->active)
                                        <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm">نشط</span>
                                    @else
                                        <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-sm">معطل</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex gap-2">
                                        <button wire:click="openModal({{ $product->id }})"
                                            class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded-lg text-sm transition">
                                            تعديل
                                        </button>
                                        <button wire:click="toggleActive({{ $product->id }})"
                                            class="px-3 py-1 rounded-lg text-sm transition {{ $product->active ? 'bg-yellow-500 hover:bg-yellow-600 text-white' : 'bg-green-500 hover:bg-green-600 text-white' }}">
                                            {{ $product->active ? 'تعطيل' : 'تفعيل' }}
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                                    لا توجد منتجات
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 bg-slate-50 border-t">
                {{ $products->links() }}
            </div>
        </div>
    </div>

    <!-- Modal -->
    @if($showModal)
        <div wire:key="product-modal-{{ $editingProduct?->id ?: 'new' }}" class="fixed inset-0 z-50 bg-black/40 flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md p-6 relative">
                <button wire:click="closeModal" class="absolute top-4 left-4 text-slate-500 hover:text-slate-900 text-2xl">&times;</button>
                <h2 class="text-2xl font-bold text-slate-800 mb-4">
                    {{ $editingProduct ? 'تعديل المنتج' : 'إضافة منتج جديد' }}
                </h2>

                <form wire:submit.prevent="saveProduct" class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-600 mb-2">اسم المنتج</label>
                        <input type="text" wire:model="name" class="w-full border border-slate-300 rounded-2xl px-4 py-3 focus:border-indigo-500 outline-none">
                        @error('name') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-600 mb-2">الفئة</label>
                        <select wire:model="category_id" class="w-full border border-slate-300 rounded-2xl px-4 py-3 focus:border-indigo-500 outline-none">
                            <option value="">اختر الفئة</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-600 mb-2">السعر</label>
                        <input type="number" step="0.01" wire:model="price" class="w-full border border-slate-300 rounded-2xl px-4 py-3 focus:border-indigo-500 outline-none" placeholder="0.00">
                        @error('price') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-600 mb-2">الحد الأدنى للمخزون</label>
                            <input type="number" step="0.01" wire:model="min_stock" class="w-full border border-slate-300 rounded-2xl px-4 py-3 focus:border-indigo-500 outline-none" placeholder="5">
                            @error('min_stock') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-600 mb-2">الوحدة</label>
                            <select wire:model="unit" class="w-full border border-slate-300 rounded-2xl px-4 py-3 focus:border-indigo-500 outline-none">
                                <option value="كيلو">كيلو</option>
                                <option value="قطعة">قطعة</option>
                                <option value="لتر">لتر</option>
                                <option value="علبة">علبة</option>
                            </select>
                            @error('unit') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-600 mb-2">نوع المنتج</label>
                        <select wire:model="product_type" class="w-full border border-slate-300 rounded-2xl px-4 py-3 focus:border-indigo-500 outline-none">
                            <option value="weight">وزني (بالكيلو/لتر)</option>
                            <option value="piece">قطعي (بالقطعة/علبة)</option>
                        </select>
                        @error('product_type') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" wire:model="active" id="active" class="rounded">
                        <label for="active" class="text-sm font-bold text-slate-600">نشط</label>
                    </div>

                    <div class="flex gap-3 mt-6">
                        <button type="submit" wire:loading.attr="disabled" wire:target="saveProduct"
                            class="flex-1 bg-[#5B45FF] hover:bg-indigo-700 text-white py-3 rounded-2xl font-bold transition">
                            {{ $editingProduct ? 'تحديث' : 'إضافة' }}
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