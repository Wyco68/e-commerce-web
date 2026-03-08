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
                            <input type="radio" name="category_filter" value="" onchange="window.location='{{ route('products.index') }}'" {{ !request('category') ? 'checked' : '' }} class="text-indigo-600">
                            All
                        </label>
                        @foreach ($categories as $category)
                            <label class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                                <input type="radio" name="category_filter" value="{{ $category->id }}" onchange="window.location='{{ route('products.index', ['category' => $category->id]) }}'" {{ request('category') == $category->id ? 'checked' : '' }} class="text-indigo-600">
                                {{ $category->name }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Price Range (visual placeholder matching wireframe) -->
                <div>
                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Price Range</h4>
                    <div class="flex items-center gap-2 text-sm">
                        <span class="text-gray-500">$0</span>
                        <div class="flex-1 h-1 bg-gray-200 dark:bg-gray-600 rounded"></div>
                        <span class="text-gray-500">$500</span>
                    </div>
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
                            <a href="{{ route('products.show', $product) }}" class="font-semibold text-gray-900 dark:text-gray-100 hover:text-indigo-600">{{ $product->name }}</a>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ $product->category->name ?? 'Uncategorized' }}</p>
                            <p class="text-indigo-600 dark:text-indigo-400 font-bold mt-1">${{ number_format($product->price, 2) }}</p>
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