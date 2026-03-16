<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Services\DiscountService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::where('user_id', $request->user()->id)
            ->with('orderItems.product')
            ->orderByDesc('date_time')
            ->get();

        return view('orders.index', compact('orders'));
    }

    public function create(Request $request)
    {
        $cart = session()->get('cart', []);
        $cartItems = [];
        $discountService = new DiscountService();

        foreach ($cart as $productId => $item) {
            $product = Product::find($productId);
            if ($product) {
                $line = $discountService->calculateLineWithDiscount(
                    $product->price,
                    $item['quantity'],
                    $product->id
                );
                $cartItems[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'line_total' => $line['discounted_total'],
                    'original_total' => $line['original_total'],
                    'savings' => $line['savings'],
                    'applied_discount_percentage' => $line['discount_percentage'],
                ];
            }
        }

        $total = array_sum(array_column($cartItems, 'line_total'));
        $totalSavings = array_sum(array_column($cartItems, 'savings'));

        return view('orders.create', compact('cartItems', 'total', 'totalSavings'));
    }

    public function store(Request $request)
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('products.index')
                ->with('error', 'Your cart is empty.');
        }

        $discountService = new DiscountService();

        $order = DB::transaction(function () use ($cart, $request, $discountService) {
            $totalPrice = 0;
            $items = [];

            foreach ($cart as $productId => $item) {
                $product = Product::findOrFail($productId);
                $lineTotal = $discountService->calculatePriceWithDiscount(
                    $product->price,
                    $item['quantity'],
                    $product->id
                );
                $totalPrice += $lineTotal;
                $items[] = [
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                ];
            }

            $order = Order::create([
                'user_id' => $request->user()->id,
                'date_time' => now(),
                'total_price' => $totalPrice,
                'status' => 'pending',
            ]);

            foreach ($items as $item) {
                $order->orderItems()->create($item);
            }

            return $order;
        });

        session()->forget('cart');

        return redirect()->route('orders.index')
            ->with('success', 'Order #' . $order->id . ' placed successfully!');
    }
}