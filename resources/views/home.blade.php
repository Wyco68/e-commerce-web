@extends('layouts.app')

@section('content')
<!-- Hero Section -->
<div class="bg-gray-800 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-center">
        <h1 class="text-4xl sm:text-5xl font-bold mb-4">Best Car Parts for Your Vehicle</h1>
        <p class="text-gray-300 text-lg mb-8">Your trusted source for quality automotive parts and accessories</p>
        <a href="{{ route('products.index') }}" class="inline-block bg-indigo-600 text-white px-8 py-3 rounded-md text-lg font-semibold hover:bg-indigo-700 transition">Shop Now</a>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <!-- Featured Categories -->
    <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">Featured Categories</h2>
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-6 mb-12">
        @foreach ($categories as $category)
            <a href="{{ route('products.index', ['category' => $category->id]) }}" class="block bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition p-4 text-center">
                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $category->name }}</span>
            </a>
        @endforeach
    </div>

    <!-- Discounted Products -->
    <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">Special Offers</h2>
    @if($discountedProducts->isEmpty())
        <p class="text-gray-600 dark:text-gray-400 mb-12">No discounted products are available right now. Check back soon.</p>
    @else
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach ($discountedProducts as $product)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
                <div class="p-4">
                    @php
                        $topDiscount = $product->discounts->sortByDesc('percentage')->first();
                    @endphp
                    <h3 class="font-semibold text-gray-900 dark:text-gray-100">{{ $product->name }}</h3>
                    <p class="text-indigo-600 dark:text-indigo-400 font-bold mt-1">${{ number_format($product->price, 2) }}</p>
                    @if($topDiscount)
                        <p class="text-xs text-green-700 dark:text-green-400 mt-1 font-medium">
                            Discount available: up to {{ rtrim(rtrim(number_format((float) $topDiscount->percentage, 2, '.', ''), '0'), '.') }}% off
                            @if($topDiscount->min_quantity > 1)
                                ({{ $topDiscount->min_quantity }}+ qty)
                            @endif
                        </p>
                    @endif
                    @auth
                        <form action="{{ route('cart.add', $product) }}" method="POST" class="mt-3">
                            @csrf
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="w-full bg-indigo-600 text-white text-sm py-2 rounded hover:bg-indigo-700 transition">Add to Cart</button>
                        </form>
                    @else
                        <a href="{{ route('products.show', $product) }}" class="mt-3 inline-block text-sm text-indigo-600 hover:underline">View Details &rarr;</a>
                    @endauth
                </div>
            </div>
        @endforeach
    </div>
    @endif

    @guest
    <div class="mt-12 text-center">
        <p class="text-gray-600 dark:text-gray-400 mb-4">Create an account to start ordering!</p>
        <a href="{{ route('register') }}" class="inline-block bg-indigo-600 text-white px-6 py-3 rounded-md hover:bg-indigo-700 transition mr-2">Register</a>
        <a href="{{ route('login') }}" class="inline-block border border-indigo-600 text-indigo-600 px-6 py-3 rounded-md hover:bg-indigo-50 dark:hover:bg-gray-800 transition">Log in</a>
    </div>
    @endguest
</div>
@endsection