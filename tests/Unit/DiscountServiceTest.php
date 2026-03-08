<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Discount;
use App\Models\Product;
use App\Services\DiscountService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiscountServiceTest extends TestCase
{
    use RefreshDatabase;

    private DiscountService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new DiscountService();
    }

    public function test_no_discount_returns_full_price(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['price' => 10.00, 'category_id' => $category->id]);

        $total = $this->service->calculatePriceWithDiscount(10.00, 3, $product->id);

        $this->assertEquals(30.00, $total);
    }

    public function test_discount_applied_when_quantity_meets_minimum(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['price' => 10.00, 'category_id' => $category->id]);
        Discount::factory()->create([
            'product_id' => $product->id,
            'min_quantity' => 5,
            'percentage' => 20,
        ]);

        // quantity = 5, meets min_quantity = 5
        // line subtotal = 10*5 = 50, discount = 20%, result = 40
        $total = $this->service->calculatePriceWithDiscount(10.00, 5, $product->id);

        $this->assertEquals(40.00, $total);
    }

    public function test_discount_not_applied_when_quantity_below_minimum(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['price' => 10.00, 'category_id' => $category->id]);
        Discount::factory()->create([
            'product_id' => $product->id,
            'min_quantity' => 5,
            'percentage' => 20,
        ]);

        // quantity = 3, below min_quantity = 5 — no discount
        $total = $this->service->calculatePriceWithDiscount(10.00, 3, $product->id);

        $this->assertEquals(30.00, $total);
    }

    public function test_best_discount_applied_when_multiple_tiers(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['price' => 10.00, 'category_id' => $category->id]);
        Discount::factory()->create([
            'product_id' => $product->id,
            'min_quantity' => 3,
            'percentage' => 10,
        ]);
        Discount::factory()->create([
            'product_id' => $product->id,
            'min_quantity' => 5,
            'percentage' => 20,
        ]);

        // quantity = 6, meets both tiers — best percentage (20%) should apply
        $total = $this->service->calculatePriceWithDiscount(10.00, 6, $product->id);

        $this->assertEquals(48.00, $total);
    }

    public function test_no_discount_row_for_product_returns_full_price(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['price' => 5.50, 'category_id' => $category->id]);

        // No discount rows exist at all for this product
        $total = $this->service->calculatePriceWithDiscount(5.50, 10, $product->id);

        $this->assertEquals(55.00, $total);
    }

    public function test_discount_for_different_product_not_applied(): void
    {
        $category = Category::factory()->create();
        $product1 = Product::factory()->create(['price' => 10.00, 'category_id' => $category->id]);
        $product2 = Product::factory()->create(['price' => 10.00, 'category_id' => $category->id]);
        Discount::factory()->create([
            'product_id' => $product1->id,
            'min_quantity' => 2,
            'percentage' => 15,
        ]);

        // product2 has no discount — should get full price
        $total = $this->service->calculatePriceWithDiscount(10.00, 5, $product2->id);

        $this->assertEquals(50.00, $total);
    }
}
