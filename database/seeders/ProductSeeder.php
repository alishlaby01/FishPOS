<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockEntry;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $productsByCategory = [
            'Fresh' => [
                'type' => 'weight', // سمك فريش بالكيلو
                'products' => [
                    ['name' => 'بلطي', 'price' => 90.00],
                    ['name' => 'بوري', 'price' => 120.00],
                    ['name' => 'دنيس', 'price' => 180.00],
                    ['name' => 'قاروص', 'price' => 220.00],
                    ['name' => 'جمبري', 'price' => 260.00],
                ]
            ],
            'Fried' => [
                'type' => 'weight', // سمك مقلي بالكيلو
                'products' => [
                    ['name' => 'بلطي مقلي', 'price' => 110.00],
                    ['name' => 'بوري مقلي', 'price' => 140.00],
                ]
            ],
            'Grilled' => [
                'type' => 'weight', // سمك مشوي بالكيلو
                'products' => [
                    ['name' => 'بلطي مشوي', 'price' => 115.00],
                    ['name' => 'بوري مشوي', 'price' => 145.00],
                ]
            ],
            'Meal' => [
                'type' => 'piece', // وجبات بالقطعة
                'products' => [
                    ['name' => 'وجبة جمبري', 'price' => 320.00],
                    ['name' => 'وجبة فيليه', 'price' => 280.00],
                ]
            ],
            'Extras' => [
                'type' => 'piece', // إضافات بالقطعة
                'products' => [
                    ['name' => 'رز', 'price' => 20.00],
                    ['name' => 'طحينة', 'price' => 10.00],
                    ['name' => 'سلطة', 'price' => 15.00],
                    ['name' => 'بطاطس', 'price' => 25.00],
                ]
            ],
            'Drinks' => [
                'type' => 'piece', // مشروبات بالقطعة
                'products' => [
                    ['name' => 'بيبسي', 'price' => 10.00],
                    ['name' => 'كوكاكولا', 'price' => 10.00],
                    ['name' => 'ماء', 'price' => 5.00],
                ]
            ],
        ];

        foreach ($productsByCategory as $categoryName => $categoryData) {
            $category = Category::query()->where('name', $categoryName)->first();

            if (! $category) {
                continue;
            }

            $productType = $categoryData['type'];
            $products = $categoryData['products'];

            foreach ($products as $product) {
                $createdProduct = Product::updateOrCreate(
                    [
                        'category_id' => $category->id,
                        'name' => $product['name'],
                    ],
                    [
                        'price' => $product['price'],
                        'product_type' => $productType,
                        'unit' => $productType === 'weight' ? 'كيلو' : 'قطعة',
                        'active' => true,
                    ]
                );

                // Add initial stock
                StockEntry::create([
                    'product_id' => $createdProduct->id,
                    'quantity' => rand(10, 50), // Random stock between 10-50
                    'type' => 'in',
                    'note' => 'Initial stock from seeder',
                ]);
            }
        }
    }
}
