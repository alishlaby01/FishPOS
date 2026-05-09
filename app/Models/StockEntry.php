<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockEntry extends Model
{
    use HasFactory;

    // الحقول اللي سمحنا بإضافتها في الـ Migration
    protected $fillable = [
        'product_id',
        'quantity',
        'type',
        'note',
    ];

    // علاقة الحركة بالمنتج
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}