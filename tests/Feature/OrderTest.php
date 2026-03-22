<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Discount;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_saves_phone_num_and_address(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'phone_num' => '555-1234',
            'address' => '123 Test St',
        ]);

        $response->assertRedirect(route('home'));

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'phone_num' => '555-1234',
            'address' => '123 Test St',
        ]);
    }

    public function test_logged_in_user_can_create_order(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'price' => 10.00,
            'category_id' => $category->id,
        ]);

        // Add item to cart via session
        $this->actingAs($user)
            ->withSession(['cart' => [$product->id => ['quantity' => 3]]])
            ->post('/orders');

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'total_price' => 30.00,
            'status' => 'pending',
        ]);

        $this->assertDatabaseHas('order_items', [
            'product_id' => $product->id,
            'quantity' => 3,
            'price' => 10.00,
        ]);
    }

    public function test_discount_applied_changes_total_price(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'price' => 10.00,
            'category_id' => $category->id,
        ]);
        Discount::factory()->create([
            'product_id' => $product->id,
            'min_quantity' => 5,
            'percentage' => 20,
        ]);

        // Quantity 5 meets min_quantity, discount = 20%
        // Expected: 10 * 5 * 0.8 = 40.00
        $this->actingAs($user)
            ->withSession(['cart' => [$product->id => ['quantity' => 5]]])
            ->post('/orders');

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'total_price' => 40.00,
        ]);
    }

    public function test_no_discount_when_quantity_below_threshold(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'price' => 10.00,
            'category_id' => $category->id,
        ]);
        Discount::factory()->create([
            'product_id' => $product->id,
            'min_quantity' => 5,
            'percentage' => 20,
        ]);

        // Quantity 2, below min_quantity 5 — no discount
        $this->actingAs($user)
            ->withSession(['cart' => [$product->id => ['quantity' => 2]]])
            ->post('/orders');

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'total_price' => 20.00,
        ]);
    }

    public function test_orders_index_only_shows_own_orders(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        Order::factory()->create(['user_id' => $user1->id, 'total_price' => 50.00, 'status' => 'pending']);
        Order::factory()->create(['user_id' => $user2->id, 'total_price' => 75.00, 'status' => 'pending']);

        $response = $this->actingAs($user1)->get('/orders');

        $response->assertStatus(200);
        $response->assertSee('50.00');
        $response->assertDontSee('75.00');
    }

    public function test_guest_cannot_access_orders(): void
    {
        $response = $this->get('/orders');

        $response->assertRedirect('/login');
    }

    public function test_guest_cannot_access_profile(): void
    {
        $response = $this->get('/profile');

        $response->assertRedirect('/login');
    }

    public function test_profile_shows_user_info(): void
    {
        $user = User::factory()->create([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone_num' => '555-9999',
            'address' => '456 Test Ave',
        ]);

        $response = $this->actingAs($user)->get('/profile');

        $response->assertStatus(200);
        $response->assertSee('Jane Doe');
        $response->assertSee('jane@example.com');
        $response->assertSee('555-9999');
        $response->assertSee('456 Test Ave');
    }
}
