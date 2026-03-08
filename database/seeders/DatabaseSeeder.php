<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Discount;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\CategorySeeder;
use Database\Seeders\ProductSeeder;
use Database\Seeders\DiscountSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\OrderSeeder;
use Database\Seeders\OrderItemSeeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            ProductSeeder::class,
            DiscountSeeder::class,
            UserSeeder::class,
            OrderSeeder::class,
            OrderItemSeeder::class,
        ]);
    }
}
