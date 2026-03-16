<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\DiscountService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        $cartItems = [];
        $discountService = new DiscountService();

        foreach ($cart as $productId => $item) {
            $product = Product::with('discounts')->find($productId);
            if ($product) {
                $line = $discountService->calculateLineWithDiscount(
                    $product->price,
                    $item['quantity'],
                    $product->id
                );
                $nextDiscount = $product->discounts
                    ->where('min_quantity', '>', $item['quantity'])
                    ->sortBy('min_quantity')
                    ->first();

                $cartItems[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'line_total' => $line['discounted_total'],
                    'original_total' => $line['original_total'],
                    'savings' => $line['savings'],
                    'applied_discount_percentage' => $line['discount_percentage'],
                    'next_discount' => $nextDiscount,
                ];
            }
        }

        $subtotal = array_sum(array_column($cartItems, 'line_total'));
        $originalSubtotal = array_sum(array_column($cartItems, 'original_total'));
        $totalSavings = $originalSubtotal - $subtotal;

        return view('cart.index', compact('cartItems', 'subtotal', 'originalSubtotal', 'totalSavings'));
    }

    public function add(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $discountService = new DiscountService();
        $cart = session()->get('cart', []);
        $qty = (int) $request->input('quantity', 1);

        if (isset($cart[$product->id])) {
            $cart[$product->id]['quantity'] += $qty;
        } else {
            $cart[$product->id] = ['quantity' => $qty];
        }

        session()->put('cart', $cart);

        $effectiveQuantity = $cart[$product->id]['quantity'];
        $appliedDiscount = $discountService->getApplicableDiscount($product->id, $effectiveQuantity);

        $message = $product->name . ' added to cart.';
        if ($appliedDiscount) {
            $percentage = rtrim(rtrim(number_format((float) $appliedDiscount->percentage, 2, '.', ''), '0'), '.');
            $message .= ' This quantity qualifies for ' . $percentage . '% discount.';
        }

        return redirect()->route('cart.index')->with('success', $message);
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $cart = session()->get('cart', []);

        if (isset($cart[$product->id])) {
            $cart[$product->id]['quantity'] = (int) $request->input('quantity');
            session()->put('cart', $cart);
        }

        return redirect()->route('cart.index');
    }

    public function remove(Product $product)
    {
        $cart = session()->get('cart', []);
        unset($cart[$product->id]);
        session()->put('cart', $cart);

        return redirect()->route('cart.index');
    }
}