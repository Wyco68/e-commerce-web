@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
    <!-- Hero Section -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-8 mb-8 text-center">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-4">Welcome to Our Store</h1>
        <p class="text-gray-600 dark:text-gray-400 mb-6">Your one-stop shop for quality car parts and accessories.</p>
        <a href="{{ route('products.index') }}" class="inline-block bg-indigo-600 text-white px-6 py-3 rounded-md hover:bg-indigo-700 transition">Browse Products</a>
    </div>

    <!-- Featured Products -->
    <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mb-6">Featured Products</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach ($featuredProducts as $product)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $product->name }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $product->category->name ?? 'Uncategorized' }}</p>
                <p class="text-gray-600 dark:text-gray-300 mt-2 text-sm">{{ Str::limit($product->description, 60) }}</p>
                <p class="text-indigo-600 dark:text-indigo-400 font-bold mt-3">${{ number_format($product->price, 2) }}</p>
                <a href="{{ route('products.show', $product) }}" class="mt-3 inline-block text-sm text-indigo-600 hover:underline">View Details &rarr;</a>
            </div>
        @endforeach
    </div>

    @guest
    <div class="mt-10 text-center">
        <p class="text-gray-600 dark:text-gray-400 mb-4">Create an account to start ordering!</p>
        <a href="{{ route('register') }}" class="inline-block bg-indigo-600 text-white px-6 py-3 rounded-md hover:bg-indigo-700 transition mr-2">Register</a>
        <a href="{{ route('login') }}" class="inline-block border border-indigo-600 text-indigo-600 px-6 py-3 rounded-md hover:bg-indigo-50 transition">Log in</a>
    </div>
    @endguest
</div>
@endsection