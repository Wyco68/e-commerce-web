<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\DiscountService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function __construct(private readonly DiscountService $discountService)
    {
    }

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
        $summary = $this->discountService->summarizeCart($cart);
        $cartItems = $summary['cartItems'];
        $total = $summary['subtotal'];
        $totalSavings = $summary['totalSavings'];

        return view('orders.create', compact('cartItems', 'total', 'totalSavings'));
    }

    public function store(Request $request)
    {
        $cart = session()->get('cart', []);
        $summary = $this->discountService->summarizeCart($cart);
        $cartItems = $summary['cartItems'];

        if (empty($cartItems)) {
            return redirect()->route('products.index')
                ->with('error', 'Your cart is empty.');
        }

        $order = DB::transaction(function () use ($request, $summary, $cartItems) {
            $totalPrice = $summary['subtotal'];

            $order = Order::create([
                'user_id' => $request->user()->id,
                'date_time' => now(),
                'total_price' => $totalPrice,
                'status' => 'pending',
            ]);

            foreach ($cartItems as $item) {
                $order->orderItems()->create([
                    'product_id' => $item['product']->id,
                    'quantity' => $item['quantity'],
                    'price' => $item['product']->price,
                ]);
            }

            return $order;
        });

        session()->forget('cart');

        return redirect()->route('orders.index')
            ->with('success', 'Order placed successfully!');
    }
}