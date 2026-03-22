<?php

namespace Database\Seeders;

use App\Models\Discount;
use Illuminate\Database\Seeder;

class DiscountSeeder extends Seeder
{
    public function run(): void
    {
        $discounts = [
            ['product_id' => 1, 'min_quantity' => 3, 'percentage' => 15],
            ['product_id' => 6, 'min_quantity' => 5, 'percentage' => 20],
            ['product_id' => 12, 'min_quantity' => 2, 'percentage' => 10],
        ];

        foreach ($discounts as $discount) {
            Discount::updateOrCreate(
                ['product_id' => $discount['product_id'], 'min_quantity' => $discount['min_quantity']],
                $discount
            );
        }
    }
}