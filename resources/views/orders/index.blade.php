@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">My Orders</h1>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
    @endif

    @if($orders->isEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-8 text-center">
            <p class="text-gray-500 dark:text-gray-400 mb-4">You have no orders yet.</p>
            <a href="{{ route('products.index') }}" class="inline-block bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700 transition">Browse Products</a>
        </div>
    @else
        <div class="space-y-6">
            @foreach ($orders as $order)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
                    <div class="flex justify-between items-center p-5 bg-gray-50 dark:bg-gray-700/50 border-b dark:border-gray-700">
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-gray-100">Order #{{ $order->id }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $order->date_time }}</p>
                        </div>
                        <div class="text-right">
                            <span class="inline-block px-2 py-1 text-xs font-medium rounded
                                {{ $order->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ ucfirst($order->status) }}
                            </span>
                            <p class="text-lg font-bold text-indigo-600 dark:text-indigo-400 mt-1">${{ number_format($order->total_price, 2) }}</p>
                        </div>
                    </div>

                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b dark:border-gray-700">
                                <th class="text-left py-2 px-5 text-gray-500 dark:text-gray-400 font-medium">Product</th>
                                <th class="text-right py-2 px-5 text-gray-500 dark:text-gray-400 font-medium">Qty</th>
                                <th class="text-right py-2 px-5 text-gray-500 dark:text-gray-400 font-medium">Unit Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->orderItems as $item)
                                <tr class="border-b dark:border-gray-700 last:border-0">
                                    <td class="py-2 px-5 text-gray-900 dark:text-gray-100">{{ $item->product->name ?? 'Deleted product' }}</td>
                                    <td class="text-right py-2 px-5 text-gray-900 dark:text-gray-100">{{ $item->quantity }}</td>
                                    <td class="text-right py-2 px-5 text-gray-900 dark:text-gray-100">${{ number_format($item->price, 2) }}</td>
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
