<?php

namespace App\Livewire\Stock;

use Livewire\Component;
use App\Models\Product;
use App\Models\StockEntry;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MorningEntry extends Component
{
    public $quantities = []; // مصفوفة لتخزين الكميات
    public $wasteQuantities = []; // مصفوفة لتخزين كميات الهالك
    public $wasteReasons = []; // أسباب الهالك
    public $activeTab = 'supply'; // التبويب النشط

    public function render()
    {
        $products = Product::query()->orderBy('name')->get();
        $productsWithoutStock = $products->filter(function ($product) {
            return (float) ($product->getRawOriginal('current_stock') ?? 0) <= 0;
        });

        return view('livewire.stock.morning-entry', compact('products', 'productsWithoutStock'))
                ->layout('layouts.app'); // تأكد إن عندك ملف اسمه app.blade.php في layouts
    }

    public function save()
    {
        // تصفية المصفوفة من القيم الفارغة أو الصفر
        $dataToSave = array_filter($this->quantities, function($qty) {
            return $qty !== null && $qty !== '';
        });

        if (empty($dataToSave)) {
            session()->flash('error', 'يرجى إدخال كمية واحدة على الأقل.');
            return;
        }

        try {
            DB::transaction(function () use ($dataToSave) {
                ksort($dataToSave, SORT_NUMERIC);
                foreach ($dataToSave as $productId => $newQty) {
                    $product = Product::query()
                        ->whereKey($productId)
                        ->lockForUpdate()
                        ->first();
                    if (!$product) {
                        continue;
                    }

                    $newQty = (float) $newQty;
                    $currentStock = (float) ($product->getRawOriginal('current_stock') ?? 0);
                    $diff = $newQty - $currentStock;

                    if ($diff != 0) {
                        StockEntry::create([
                            'product_id' => $productId,
                            'quantity' => abs($diff),
                            'type' => $diff > 0 ? 'in' : 'out',
                            'note' => 'تحديث المخزون الصباحي بتاريخ ' . now()->format('Y-m-d'),
                        ]);

                        $product->update(['current_stock' => $newQty]);
                    }
                }
            });

            $this->quantities = [];
            session()->flash('message', 'تم تحديث المخزون بنجاح ✅');
            $this->dispatch('toast', ['message' => 'تم تحديث المخزون بنجاح ✅', 'type' => 'success']);
        } catch (\Throwable $e) {
            Log::error('MorningEntry::save failed', ['exception' => $e]);
            session()->flash('error', 'حدث خطأ أثناء الحفظ، يرجى المحاولة مرة أخرى.');
            $this->dispatch('toast', ['message' => 'حدث خطأ أثناء الحفظ، يرجى المحاولة مرة أخرى.', 'type' => 'error']);
        }
    }

    public function saveWaste()
    {
        // تصفية المصفوفة من القيم الفارغة أو الصفر
        $dataToSave = array_filter($this->wasteQuantities, function($qty) {
            return $qty > 0;
        });

        if (empty($dataToSave)) {
            session()->flash('error', 'يرجى إدخال كمية هالك واحدة على الأقل.');
            return;
        }

        try {
            DB::transaction(function () use ($dataToSave) {
                ksort($dataToSave, SORT_NUMERIC);
                foreach ($dataToSave as $productId => $qty) {
                    $product = Product::query()
                        ->whereKey($productId)
                        ->lockForUpdate()
                        ->first();
                    if (!$product) {
                        continue;
                    }

                    $qty = (float) $qty;
                    $reason = $this->wasteReasons[$productId] ?? 'غير محدد';

                    StockEntry::create([
                        'product_id' => $productId,
                        'quantity' => $qty,
                        'type' => 'waste',
                        'note' => 'هالك: ' . $reason . ' - بتاريخ ' . now()->format('Y-m-d'),
                    ]);

                    $currentStock = (float) ($product->getRawOriginal('current_stock') ?? 0);
                    $product->update(['current_stock' => max(0, $currentStock - $qty)]);
                }
            });

            $this->wasteQuantities = [];
            $this->wasteReasons = [];
            session()->flash('message', 'تم تسجيل الهالك بنجاح ✅');
            $this->dispatch('toast', ['message' => 'تم تسجيل الهالك بنجاح ✅', 'type' => 'success']);
        } catch (\Throwable $e) {
            Log::error('MorningEntry::saveWaste failed', ['exception' => $e]);
            session()->flash('error', 'حدث خطأ أثناء التسجيل، يرجى المحاولة مرة أخرى.');
            $this->dispatch('toast', ['message' => 'حدث خطأ أثناء التسجيل، يرجى المحاولة مرة أخرى.', 'type' => 'error']);
        }
    }
}