<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'order_type',
        'customer_name',
        'phone',
        'address',
        'subtotal',
        'discount',
        'delivery_fee',
        'total',
        'status',
        'created_by',
        'shift_id',          // جديد
        'driver_id',         // جديد
        'driver_commission', // جديد
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'driver_commission' => 'decimal:2', // جديد
        'total' => 'decimal:2',
    ];

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // علاقة الوردية
    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    // علاقة الطيار
    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }
}