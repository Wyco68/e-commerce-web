<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DiscountController extends Controller
{
    public function applyDiscount($code)
    {
        // Logic to validate and apply discount
        return response()->json(['success' => true, 'message' => 'Discount applied!']);
    }
}