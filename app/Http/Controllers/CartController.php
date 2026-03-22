<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\DiscountService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(private readonly DiscountService $discountService)
    {
    }

    public function index()
    {
        $cart = session()->get('cart', []);
        $summary = $this->discountService->summarizeCart($cart, true);
        $cartItems = $summary['cartItems'];
        $subtotal = $summary['subtotal'];
        $originalSubtotal = $summary['originalSubtotal'];
        $totalSavings = $summary['totalSavings'];

        return view('cart.index', compact('cartItems', 'subtotal', 'originalSubtotal', 'totalSavings'));
    }

    public function add(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $cart = session()->get('cart', []);
        $qty = (int) $request->input('quantity', 1);

        if (isset($cart[$product->id])) {
            $cart[$product->id]['quantity'] += $qty;
        } else {
            $cart[$product->id] = ['quantity' => $qty];
        }

        session()->put('cart', $cart);

        $effectiveQuantity = $cart[$product->id]['quantity'];
        $appliedDiscount = $this->discountService->getApplicableDiscount($product->id, $effectiveQuantity);

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