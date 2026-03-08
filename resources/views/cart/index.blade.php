@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">Shopping Cart</h1>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
    @endif

    @if(empty($cartItems))
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-8 text-center">
            <p class="text-gray-500 dark:text-gray-400 mb-4">Your cart is empty.</p>
            <a href="{{ route('products.index') }}" class="inline-block bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700 transition">Browse Products</a>
        </div>
    @else
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm">
            <!-- Cart Items -->
            @foreach ($cartItems as $item)
                <div class="flex items-center gap-4 p-5 border-b dark:border-gray-700 last:border-0">
                    <!-- Product Info -->
                    <div class="flex-1 min-w-0">
                        <h3 class="font-semibold text-gray-900 dark:text-gray-100">{{ $item['product']->name }}</h3>
                        <p class="text-sm text-indigo-600 dark:text-indigo-400 font-medium">${{ number_format($item['product']->price, 2) }}</p>
                    </div>

                    <!-- Quantity Control -->
                    <form action="{{ route('cart.update', $item['product']) }}" method="POST" class="flex items-center gap-2">
                        @csrf
                        @method('PATCH')
                        <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" class="w-16 border border-gray-300 dark:border-gray-600 rounded px-2 py-1 text-sm text-center dark:bg-gray-700 dark:text-gray-200">
                        <button type="submit" class="text-sm text-indigo-600 hover:underline">Update</button>
                    </form>

                    <!-- Remove -->
                    <form action="{{ route('cart.remove', $item['product']) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-sm text-red-500 hover:text-red-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </form>
                </div>
            @endforeach

            <!-- Subtotal & Actions -->
            <div class="p-5 border-t dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 rounded-b-lg">
                <div class="flex justify-between items-center mb-4">
                    <span class="font-semibold text-gray-900 dark:text-gray-100">Subtotal</span>
                    <span class="text-lg font-bold text-gray-900 dark:text-gray-100">
                        ${{ number_format(collect($cartItems)->sum(fn($i) => $i['product']->price * $i['quantity']), 2) }}
                    </span>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('products.index') }}" class="flex-1 text-center border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700 transition">Continue Shopping</a>
                    <a href="{{ route('orders.create') }}" class="flex-1 text-center bg-indigo-600 text-white py-2 rounded hover:bg-indigo-700 transition font-medium">Checkout</a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection