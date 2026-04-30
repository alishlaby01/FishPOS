<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
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
                ['name' => 'بلطي', 'price' => 90.00],
                ['name' => 'بوري', 'price' => 120.00],
                ['name' => 'دنيس', 'price' => 180.00],
                ['name' => 'قاروص', 'price' => 220.00],
                ['name' => 'جمبري', 'price' => 260.00],
            ],
            'Fried' => [
                ['name' => 'بلطي مقلي', 'price' => 110.00],
                ['name' => 'بوري مقلي', 'price' => 140.00],
            ],
            'Grilled' => [
                ['name' => 'بلطي مشوي', 'price' => 115.00],
                ['name' => 'بوري مشوي', 'price' => 145.00],
            ],
            'Meal' => [
                ['name' => 'وجبة جمبري', 'price' => 320.00],
                ['name' => 'وجبة فيليه', 'price' => 280.00],
            ],
            'Extras' => [
                ['name' => 'رز', 'price' => 20.00],
                ['name' => 'طحينة', 'price' => 10.00],
                ['name' => 'سلطة', 'price' => 15.00],
                ['name' => 'بطاطس', 'price' => 25.00],
            ],
        ];

        foreach ($productsByCategory as $categoryName => $products) {
            $category = Category::query()->where('name', $categoryName)->first();

            if (! $category) {
                continue;
            }

            foreach ($products as $product) {
                Product::updateOrCreate(
                    [
                        'category_id' => $category->id,
                        'name' => $product['name'],
                    ],
                    [
                        'price' => $product['price'],
                        'active' => true,
                    ]
                );
            }
        }
    }
}
