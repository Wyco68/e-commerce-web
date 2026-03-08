@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">Products</h1>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($products as $product)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $product->name }}</h3>
                <p class="text-sm text-indigo-500 mt-1">{{ $product->category->name ?? 'Uncategorized' }}</p>
                <p class="text-gray-600 dark:text-gray-300 mt-2 text-sm">{{ $product->description }}</p>
                <p class="text-indigo-600 dark:text-indigo-400 font-bold mt-3">${{ number_format($product->price, 2) }}</p>

                <div class="mt-4 flex items-center gap-2">
                    <a href="{{ route('products.show', $product) }}" class="text-sm text-indigo-600 hover:underline">Details</a>
                    @auth
                        <form action="{{ route('cart.add', $product) }}" method="POST" class="flex items-center gap-2">
                            @csrf
                            <input type="number" name="quantity" value="1" min="1" class="w-16 border border-gray-300 rounded px-2 py-1 text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">
                            <button type="submit" class="bg-indigo-600 text-white text-sm px-3 py-1 rounded hover:bg-indigo-700">Add to Cart</button>
                        </form>
                    @endauth
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection