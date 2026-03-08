<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function index()
    {
        return view('checkout.index');
    }

    public function process(Request $request)
    {
        $address = $request->input('address');
        $discountCode = $request->input('discount_code');

        // Process the order and apply discount logic here

        return redirect()->route('home')->with('success', 'Order placed successfully!');
    }
}