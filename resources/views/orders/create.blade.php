@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">Review & Confirm Order</h1>

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ session('error') }}</div>
    @endif

    @if(empty($cartItems))
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-8 text-center">
            <p class="text-gray-500 dark:text-gray-400 mb-4">Your cart is empty. Add some products first!</p>
            <a href="{{ route('products.index') }}" class="inline-block bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700 transition">Browse Products</a>
        </div>
    @else
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="text-left py-3 px-5 text-gray-500 dark:text-gray-400 font-medium">Product</th>
                        <th class="text-right py-3 px-5 text-gray-500 dark:text-gray-400 font-medium">Qty</th>
                        <th class="text-right py-3 px-5 text-gray-500 dark:text-gray-400 font-medium">Unit Price</th>
                        <th class="text-right py-3 px-5 text-gray-500 dark:text-gray-400 font-medium">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($cartItems as $item)
                        <tr class="border-b dark:border-gray-700">
                            <td class="py-3 px-5 text-gray-900 dark:text-gray-100">{{ $item['product']->name }}</td>
                            <td class="text-right py-3 px-5 text-gray-900 dark:text-gray-100">{{ $item['quantity'] }}</td>
                            <td class="text-right py-3 px-5 text-gray-900 dark:text-gray-100">${{ number_format($item['product']->price, 2) }}</td>
                            <td class="text-right py-3 px-5 text-gray-900 dark:text-gray-100">
                                @if($item['line_total'] < $item['original_total'])
                                    <span class="line-through text-gray-400">${{ number_format($item['original_total'], 2) }}</span>
                                    <span class="text-green-600 font-medium">${{ number_format($item['line_total'], 2) }}</span>
                                @else
                                    ${{ number_format($item['line_total'], 2) }}
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="p-5 border-t dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                <div class="flex justify-between items-center mb-4">
                    <span class="font-semibold text-gray-900 dark:text-gray-100">Total</span>
                    <span class="text-xl font-bold text-indigo-600 dark:text-indigo-400">${{ number_format($total, 2) }}</span>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('cart.index') }}" class="flex-1 text-center border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700 transition">Back to Cart</a>
                    <form action="{{ route('orders.store') }}" method="POST" class="flex-1">
                        @csrf
                        <button type="submit" class="w-full bg-indigo-600 text-white py-2 rounded hover:bg-indigo-700 transition font-medium">Confirm Order</button>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
