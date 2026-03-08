<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['name' => 'Car Air Freshener', 'description' => 'Lemon fragrance', 'price' => 3.50, 'category_id' => 1],
            ['name' => 'Seat Cushion', 'description' => 'Comfortable seat cushion', 'price' => 15.00, 'category_id' => 1],
            ['name' => 'Steering Wheel Cover', 'description' => 'Non-slip cover', 'price' => 8.00, 'category_id' => 1],
            ['name' => 'Car Trash Bin', 'description' => 'Compact trash bin', 'price' => 5.00, 'category_id' => 1],
            ['name' => 'Cup Holder', 'description' => 'Adjustable cup holder', 'price' => 4.00, 'category_id' => 1],
            ['name' => 'Microfiber Cloth', 'description' => 'Car cleaning cloth', 'price' => 2.00, 'category_id' => 2],
            ['name' => 'Car Wash Sponge', 'description' => 'Soft sponge', 'price' => 3.00, 'category_id' => 2],
            ['name' => 'Cleaning Brush', 'description' => 'Multi-purpose brush', 'price' => 4.50, 'category_id' => 2],
            ['name' => 'Tire Brush', 'description' => 'Durable tire brush', 'price' => 6.00, 'category_id' => 2],
            ['name' => 'Cleaning Spray Bottle', 'description' => 'Reusable spray bottle', 'price' => 3.50, 'category_id' => 2],
            ['name' => 'Phone Holder', 'description' => 'Adjustable phone holder', 'price' => 10.00, 'category_id' => 3],
            ['name' => 'USB Car Charger', 'description' => 'Dual port charger', 'price' => 8.00, 'category_id' => 3],
            ['name' => 'Charging Cable', 'description' => 'Fast charging cable', 'price' => 5.00, 'category_id' => 3],
            ['name' => 'Dashboard Mat', 'description' => 'Non-slip mat', 'price' => 7.00, 'category_id' => 3],
            ['name' => 'Key Holder', 'description' => 'Stylish key holder', 'price' => 3.50, 'category_id' => 3],
            ['name' => 'Emergency Hammer', 'description' => 'Window breaker tool', 'price' => 7.50, 'category_id' => 4],
            ['name' => 'Seat Belt Cutter', 'description' => 'Safety cutter', 'price' => 6.00, 'category_id' => 4],
            ['name' => 'Reflective Warning Triangle', 'description' => 'Foldable triangle', 'price' => 12.00, 'category_id' => 4],
            ['name' => 'Tire Pressure Gauge', 'description' => 'Accurate gauge', 'price' => 5.50, 'category_id' => 4],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}