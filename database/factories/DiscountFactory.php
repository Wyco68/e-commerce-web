<?php

namespace Database\Factories;

use App\Models\Discount;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class DiscountFactory extends Factory
{
    protected $model = Discount::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'min_quantity' => fake()->numberBetween(2, 10),
            'percentage' => fake()->numberBetween(5, 30),
        ];
    }
}
