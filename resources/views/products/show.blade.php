@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
    <a href="{{ route('products.index') }}" class="text-sm text-indigo-600 hover:underline">&larr; Back to Products</a>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-8 mt-4">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $product->name }}</h1>
        <p class="text-sm text-indigo-500 mt-1">{{ $product->category->name ?? 'Uncategorized' }}</p>
        <p class="text-gray-600 dark:text-gray-300 mt-4">{{ $product->description }}</p>
        <p class="text-xl text-indigo-600 dark:text-indigo-400 font-bold mt-4">${{ number_format($product->price, 2) }}</p>

        @if($product->discounts->count())
            <div class="mt-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded p-3">
                <p class="text-sm font-medium text-green-800 dark:text-green-300">Quantity Discounts:</p>
                @foreach($product->discounts as $discount)
                    <p class="text-sm text-green-700 dark:text-green-400">Buy {{ $discount->min_quantity }}+ and get {{ $discount->percentage }}% off!</p>
                @endforeach
            </div>
        @endif

        @auth
            <form action="{{ route('cart.add', $product) }}" method="POST" class="mt-6 flex items-center gap-3">
                @csrf
                <input type="number" name="quantity" value="1" min="1" class="w-20 border border-gray-300 rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">
                <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700">Add to Cart</button>
            </form>
        @else
            <p class="mt-6 text-gray-500"><a href="{{ route('login') }}" class="text-indigo-600 hover:underline">Log in</a> to purchase this product.</p>
        @endauth
    </div>
</div>
@endsection