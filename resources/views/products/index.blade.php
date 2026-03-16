@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
    @endif

    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Sidebar Filters -->
        <aside class="w-full lg:w-64 shrink-0">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-5">
                <h3 class="font-bold text-gray-900 dark:text-gray-100 mb-4">Filters</h3>

                <!-- Categories -->
                <div class="mb-6">
                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Categories</h4>
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                            <input type="radio" name="category_filter" value="" onchange="window.location='{{ route('products.index', array_filter(['min_price' => request('min_price'), 'max_price' => request('max_price')])) }}'" {{ !request('category') ? 'checked' : '' }} class="text-indigo-600">
                            All
                        </label>
                        @foreach ($categories as $category)
                            <label class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                                <input type="radio" name="category_filter" value="{{ $category->id }}" onchange="window.location='{{ route('products.index', array_filter(['category' => $category->id, 'min_price' => request('min_price'), 'max_price' => request('max_price')])) }}'" {{ request('category') == $category->id ? 'checked' : '' }} class="text-indigo-600">
                                {{ $category->name }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Price Range -->
                <div>
                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Price Range</h4>
                    <form method="GET" action="{{ route('products.index') }}">
                        @if(request('category'))
                            <input type="hidden" name="category" value="{{ request('category') }}">
                        @endif
                        <div class="flex items-center gap-2 text-sm mb-2">
                            <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min" min="0" step="0.01" class="w-full border border-gray-300 dark:border-gray-600 rounded px-2 py-1 text-sm dark:bg-gray-700 dark:text-gray-200">
                            <span class="text-gray-400">-</span>
                            <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Max" min="0" step="0.01" class="w-full border border-gray-300 dark:border-gray-600 rounded px-2 py-1 text-sm dark:bg-gray-700 dark:text-gray-200">
                        </div>
                        <button type="submit" class="w-full bg-indigo-600 text-white text-sm py-1.5 rounded hover:bg-indigo-700 transition">Apply</button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Product Listing -->
        <div class="flex-1">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">Products</h1>

            <div class="space-y-4">
                @foreach ($products as $product)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-5 flex items-center gap-5">
                        <div class="flex-1 min-w-0">
                            @php
                                $topDiscount = $product->discounts->sortByDesc('percentage')->first();
                            @endphp
                            <a href="{{ route('products.show', $product) }}" class="font-semibold text-gray-900 dark:text-gray-100 hover:text-indigo-600">{{ $product->name }}</a>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ $product->category->name ?? 'Uncategorized' }}</p>
                            <p class="text-indigo-600 dark:text-indigo-400 font-bold mt-1">${{ number_format($product->price, 2) }}</p>
                            @if($topDiscount)
                                <p class="text-sm text-green-700 dark:text-green-400 mt-1 font-medium">
                                    On discount: up to {{ rtrim(rtrim(number_format((float) $topDiscount->percentage, 2, '.', ''), '0'), '.') }}% off
                                    @if($topDiscount->min_quantity > 1)
                                        when you buy {{ $topDiscount->min_quantity }}+.
                                    @else
                                        on this item.
                                    @endif
                                </p>
                            @endif
                        </div>

                        @auth
                            <form action="{{ route('cart.add', $product) }}" method="POST" class="flex items-center gap-2 shrink-0">
                                @csrf
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="bg-indigo-600 text-white text-sm px-4 py-2 rounded hover:bg-indigo-700 transition">Add to Cart</button>
                            </form>
                        @endauth
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection