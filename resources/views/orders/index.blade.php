@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">My Orders</h1>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
    @endif

    @if($orders->isEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 text-center">
            <p class="text-gray-500 dark:text-gray-400">You have no orders yet.</p>
            <a href="{{ route('products.index') }}" class="mt-4 inline-block text-indigo-600 hover:underline">Browse Products</a>
        </div>
    @else
        <div class="space-y-6">
            @foreach ($orders as $order)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-gray-100">Order #{{ $order->id }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $order->date_time }}</p>
                        </div>
                        <div class="text-right">
                            <span class="inline-block px-2 py-1 text-xs font-medium rounded
                                {{ $order->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ ucfirst($order->status) }}
                            </span>
                            <p class="text-lg font-bold text-gray-900 dark:text-gray-100 mt-1">${{ number_format($order->total_price, 2) }}</p>
                        </div>
                    </div>

                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b dark:border-gray-700">
                                <th class="text-left py-2 text-gray-500 dark:text-gray-400">Product</th>
                                <th class="text-right py-2 text-gray-500 dark:text-gray-400">Qty</th>
                                <th class="text-right py-2 text-gray-500 dark:text-gray-400">Unit Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->orderItems as $item)
                                <tr class="border-b dark:border-gray-700">
                                    <td class="py-2 text-gray-900 dark:text-gray-100">{{ $item->product->name ?? 'Deleted product' }}</td>
                                    <td class="text-right py-2 text-gray-900 dark:text-gray-100">{{ $item->quantity }}</td>
                                    <td class="text-right py-2 text-gray-900 dark:text-gray-100">${{ number_format($item->price, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
