<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        $discountedProducts = Product::with('category', 'discounts')
            ->whereHas('discounts')
            ->withMax('discounts', 'percentage')
            ->orderByDesc('discounts_max_percentage')
            ->take(4)
            ->get();

        return view('home', compact('categories', 'discountedProducts'));
    }
}