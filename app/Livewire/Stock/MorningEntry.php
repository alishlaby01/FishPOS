<?php

namespace App\Livewire\Stock;

use App\Models\Product;
use App\Models\StockEntry;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class MorningEntry extends Component
{
    public $quantities = []; // مصفوفة لتخزين الكميات

    public function render()
    {
        $products = Product::query()->with('stockEntries')->orderBy('name')->get();
        $productsWithoutStock = $products->filter(function ($product) {
            return $product->displayStock() <= 0;
        });

        return view('livewire.stock.morning-entry', compact('products', 'productsWithoutStock'))
            ->layout('layouts.app');
    }

    public function save()
    {
        $dataToSave = array_filter($this->quantities, function ($qty) {
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
                    if (! $product) {
                        continue;
                    }

                    $newQty = (float) $newQty;
                    $product->load('stockEntries');
                    $currentStock = $product->recalculateCurrentStockFromEntries();
                    $diff = $newQty - $currentStock;

                    if ($diff != 0) {
                        StockEntry::create([
                            'product_id' => $productId,
                            'quantity' => abs($diff),
                            'type' => $diff > 0 ? 'in' : 'out',
                            'note' => 'تحديث المخزون الصباحي بتاريخ '.now()->format('Y-m-d'),
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
}
