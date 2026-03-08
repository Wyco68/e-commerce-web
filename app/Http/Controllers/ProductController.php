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
        $products = $query->get();

        return view('products.index', compact('products', 'categories'));
    }

    public function show(Product $product)
    {
        $product->load('category', 'discounts');
        return view('products.show', compact('product'));
    }
}