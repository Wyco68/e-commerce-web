<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::all();

        $query = Product::with('category');
        if ($request->filled('category')) {
            $query->where('category_id', $request->integer('category'));
        }
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->input('min_price'));
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->input('max_price'));
        }
        $products = $query->get();

        return view('products.index', compact('products', 'categories'));
    }

    public function show(Product $product)
    {
        $product->load('category', 'discounts');
        return view('products.show', compact('product'));
    }
}