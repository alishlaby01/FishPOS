<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'category_id',
        'name',
        'price',
        'current_stock',
        'min_stock',
        'unit',
        'product_type', // إضافة نوع المنتج
        'active',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
        'current_stock' => 'decimal:2',
        'min_stock' => 'decimal:2',
        'active' => 'boolean',
        'product_type' => 'string',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function stockEntries(): HasMany
    {
        return $this->hasMany(StockEntry::class);
    }

    /**
     * الرصيد من حركات المخزن فقط (مصدر واحد للحقيقة بعد تسجيل وارد/صادر/هالك).
     */
    public function recalculateCurrentStockFromEntries(): float
    {
        $in = (float) $this->stockEntries()->where('type', 'in')->sum('quantity');
        $out = (float) $this->stockEntries()->whereIn('type', ['out', 'waste'])->sum('quantity');

        return max(0, $in - $out);
    }

    /**
     * للعرض في الواجهات: يعتمد على الحركات المحمّلة إن وُجدت وإلا على عمود current_stock.
     */
    public function displayStock(): float
    {
        if ($this->relationLoaded('stockEntries')) {
            $in = (float) $this->stockEntries->where('type', 'in')->sum('quantity');
            $out = (float) $this->stockEntries->whereIn('type', ['out', 'waste'])->sum('quantity');

            return max(0, $in - $out);
        }

        return (float) ($this->current_stock ?? 0);
    }
}
