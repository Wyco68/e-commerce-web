<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Discount;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_loads_and_shows_products(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'name' => 'Test Widget',
            'category_id' => $category->id,
        ]);
        Discount::factory()->create([
            'product_id' => $product->id,
            'min_quantity' => 1,
            'percentage' => 10,
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Best Car Parts for Your Vehicle');
        $response->assertSee('Test Widget');
    }

    public function test_products_index_loads(): void
    {
        $category = Category::factory()->create(['name' => 'Electronics']);
        Product::factory()->create([
            'name' => 'Laptop',
            'price' => 25.00,
            'category_id' => $category->id,
        ]);

        $response = $this->get('/products');

        $response->assertStatus(200);
        $response->assertSee('Laptop');
        $response->assertSee('Electronics');
        $response->assertSee('25.00');
    }

    public function test_product_show_page_loads(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'name' => 'Phone Holder',
            'category_id' => $category->id,
        ]);

        $response = $this->get('/products/' . $product->id);

        $response->assertStatus(200);
        $response->assertSee('Phone Holder');
    }
}