<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class ProductManager extends Component
{
    use WithPagination;

    public $showModal = false;
    public $editingProduct = null;
    public $name = '';
    public $price = '';
    public $purchase_price = '';
    public $category_id = '';
    public $active = true;
    public $min_stock = 5;
    public $unit = 'كيلو';
    public $product_type = 'weight';
    public $search = '';

    protected $rules = [
        'name' => 'required|string|max:255',
        'price' => 'required|numeric|min:0.01',
        'purchase_price' => 'required|numeric|min:0',
        'category_id' => 'required|exists:categories,id',
        'active' => 'boolean',
        'min_stock' => 'required|numeric|min:0',
        'unit' => 'required|string|max:20',
        'product_type' => 'required|in:weight,piece',
    ];

    public function mount()
    {
        // التحقق من أن المستخدم هو OWNER
        if (Auth::user()?->role !== 'owner') {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الصفحة');
        }
    }

    public function openModal($productId = null)
    {
        $this->resetValidation();
        $this->reset(['name', 'price', 'purchase_price', 'category_id', 'active', 'min_stock', 'unit', 'product_type']);

        if ($productId) {
            $product = Product::findOrFail($productId);
            $this->editingProduct = $product;
            $this->name = $product->name;
            $this->price = $product->price;
            $this->purchase_price = $product->purchase_price;
            $this->category_id = $product->category_id;
            $this->active = $product->active;
            $this->min_stock = $product->min_stock ?? 5;
            $this->unit = $product->unit ?? 'كيلو';
            $this->product_type = $product->product_type ?? 'weight';
        } else {
            $this->editingProduct = null;
        }

        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->editingProduct = null;
        $this->reset(['name', 'price', 'purchase_price', 'category_id', 'active', 'min_stock', 'unit', 'product_type']);
    }

    public function updatedCategoryId($value)
    {
        if ($value) {
            $category = Category::find($value);
            if ($category) {
                // تحديد النوع تلقائيًا بناءً على اسم الفئة
                $fishCategories = ['Fresh', 'Fried', 'Grilled'];
                $this->product_type = in_array($category->name, $fishCategories) ? 'weight' : 'piece';
                $this->unit = $this->product_type === 'weight' ? 'كيلو' : 'قطعة';
            }
        }
    }

    public function saveProduct()
    {
        $this->validate();

        if ($this->editingProduct) {
            $this->editingProduct->update([
                'name' => $this->name,
                'price' => $this->price,
                'purchase_price' => $this->purchase_price,
                'category_id' => $this->category_id,
                'active' => $this->active,
                'min_stock' => $this->min_stock,
                'unit' => $this->unit,
                'product_type' => $this->product_type,
            ]);
            session()->flash('message', 'تم تحديث المنتج بنجاح.');
            $this->dispatch('toast', ['message' => 'تم تحديث المنتج بنجاح.', 'type' => 'success']);
        } else {
            Product::create([
                'name' => $this->name,
                'price' => $this->price,
                'purchase_price' => $this->purchase_price,
                'category_id' => $this->category_id,
                'active' => $this->active,
                'min_stock' => $this->min_stock,
                'unit' => $this->unit,
                'product_type' => $this->product_type,
            ]);
            session()->flash('message', 'تم إضافة المنتج بنجاح.');
            $this->dispatch('toast', ['message' => 'تم إضافة المنتج بنجاح.', 'type' => 'success']);
        }

        $this->closeModal();
    }

    public function deleteProduct($productId)
    {
        if (Auth::user()->role !== 'owner') {
            abort(403);
        }
        $product = Product::findOrFail($productId);
        $product->delete(); // Soft delete
        session()->flash('message', 'تم حذف المنتج بنجاح.');
        $this->resetPage();
    }

    public function forceDeleteProduct($productId)
    {
        if (Auth::user()?->role !== 'owner') {
            abort(403);
        }

        $product = Product::withTrashed()->findOrFail($productId);
        $product->forceDelete(); // Hard delete
        session()->flash('message', 'تم حذف المنتج نهائياً.');
    }

    public function restoreProduct($productId)
    {
        if (Auth::user()?->role !== 'owner') {
            abort(403);
        }

        $product = Product::withTrashed()->findOrFail($productId);
        $product->restore();
        session()->flash('message', 'تم استعادة المنتج.');
    }

    public function toggleActive($productId)
    {
        if (Auth::user()?->role !== 'owner') {
            abort(403);
        }

        $product = Product::findOrFail($productId);
        $product->update(['active' => !$product->active]);
        session()->flash('message', 'تم تحديث حالة المنتج.');
    }

    public function render()
    {
        $products = Product::withoutTrashed()
            ->with('category', 'stockEntries') // إضافة stockEntries للحساب الديناميكي
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $categories = Category::where('active', true)->get();

        return view('livewire.product-manager', compact('products', 'categories'));
    }
}