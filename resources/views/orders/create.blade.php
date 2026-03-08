@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">Review & Confirm Order</h1>

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ session('error') }}</div>
    @endif

    @if(empty($cartItems))
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 text-center">
            <p class="text-gray-500 dark:text-gray-400">Your cart is empty. Add some products first!</p>
            <a href="{{ route('products.index') }}" class="mt-4 inline-block text-indigo-600 hover:underline">Browse Products</a>
        </div>
    @else
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <table class="w-full text-sm mb-6">
                <thead>
                    <tr class="border-b dark:border-gray-700">
                        <th class="text-left py-2 text-gray-500 dark:text-gray-400">Product</th>
                        <th class="text-right py-2 text-gray-500 dark:text-gray-400">Qty</th>
                        <th class="text-right py-2 text-gray-500 dark:text-gray-400">Unit Price</th>
                        <th class="text-right py-2 text-gray-500 dark:text-gray-400">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($cartItems as $item)
                        <tr class="border-b dark:border-gray-700">
                            <td class="py-2 text-gray-900 dark:text-gray-100">{{ $item['product']->name }}</td>
                            <td class="text-right py-2 text-gray-900 dark:text-gray-100">{{ $item['quantity'] }}</td>
                            <td class="text-right py-2 text-gray-900 dark:text-gray-100">${{ number_format($item['product']->price, 2) }}</td>
                            <td class="text-right py-2 text-gray-900 dark:text-gray-100">
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
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-right py-3 font-bold text-gray-900 dark:text-gray-100">Total:</td>
                        <td class="text-right py-3 font-bold text-lg text-indigo-600 dark:text-indigo-400">${{ number_format($total, 2) }}</td>
                    </tr>
                </tfoot>
            </table>

            <div class="flex justify-between items-center">
                <a href="{{ route('cart.index') }}" class="text-sm text-indigo-600 hover:underline">&larr; Back to Cart</a>
                <form action="{{ route('orders.store') }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700 font-medium">Confirm Order</button>
                </form>
            </div>
        </div>
    @endif
</div>
@endsection
