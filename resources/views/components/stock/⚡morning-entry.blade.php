{{-- resources/views/livewire/stock/morning-entry.blade.php --}}
<div class="p-10 bg-surface min-h-screen font-cairo" dir="rtl">
    <h3 class="text-2xl font-bold text-accent mb-8 flex items-center gap-2">
        <span class="material-symbols-outlined">inventory</span>
        تسجيل مخزون الصباح
    </h3>

    <div class="neumorphic-lift bg-stone-900 rounded-[32px] overflow-hidden">
        <table class="w-full text-right border-collapse">
            <thead>
                <tr class="bg-stone-950 text-stone-400">
                    <th class="p-6">المنتج</th>
                    <th class="p-6">الكمية الحالية</th>
                    <th class="p-6 text-center">إضافة كمية جديدة</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-800">
                @foreach($products as $product)
                <tr class="hover:bg-stone-800/30 transition-colors">
                    <td class="p-6 font-bold text-stone-100">{{ $product->name }}</td>
                    <td class="p-6 text-stone-400">{{ $product->current_stock ?? 0 }} كيلو</td>
                    <td class="p-6 flex justify-center">
                        <input type="number" wire:model="stock.{{ $product->id }}" 
                               class="bg-stone-950 neumorphic-sunken border-none rounded-xl w-32 text-center text-accent focus:ring-1 focus:ring-accent/50">
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <div class="mt-8 flex justify-end">
        <button wire:click="save" class="px-12 py-4 bg-accent text-stone-950 font-black rounded-full shadow-lg hover:scale-105 transition-transform">
            حفظ الكميات
        </button>
    </div>
</div>