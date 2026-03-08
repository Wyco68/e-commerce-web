@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">Shopping Cart</h1>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
    @endif

    @if(empty($cartItems))
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 text-center">
            <p class="text-gray-500 dark:text-gray-400">Your cart is empty.</p>
            <a href="{{ route('products.index') }}" class="mt-4 inline-block text-indigo-600 hover:underline">Browse Products</a>
        </div>
    @else
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            @foreach ($cartItems as $item)
                <div class="flex items-center justify-between py-4 border-b dark:border-gray-700 last:border-0">
                    <div>
                        <h3 class="font-medium text-gray-900 dark:text-gray-100">{{ $item['product']->name }}</h3>
                        <p class="text-sm text-gray-500">${{ number_format($item['product']->price, 2) }} each</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <form action="{{ route('cart.update', $item['product']) }}" method="POST" class="flex items-center gap-2">
                            @csrf
                            @method('PATCH')
                            <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" class="w-16 border border-gray-300 rounded px-2 py-1 text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">
                            <button type="submit" class="text-sm text-indigo-600 hover:underline">Update</button>
                        </form>
                        <form action="{{ route('cart.remove', $item['product']) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-sm text-red-600 hover:underline">Remove</button>
                        </form>
                    </div>
                </div>
            @endforeach

            <div class="mt-6 flex justify-end">
                <a href="{{ route('orders.create') }}" class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700 font-medium">Proceed to Checkout</a>
            </div>
        </div>
    @endif
</div>
@endsection