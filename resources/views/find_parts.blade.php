@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">Find Parts for Your Car</h1>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-8">
        <form method="GET" action="{{ route('parts.search') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Brand</label>
                <select name="brand" class="w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 text-sm dark:bg-gray-700 dark:text-gray-200">
                    <option>Toyota</option>
                    <option>Honda</option>
                    <option>Ford</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Model</label>
                <select name="model" class="w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 text-sm dark:bg-gray-700 dark:text-gray-200">
                    <option>Camry</option>
                    <option>Corolla</option>
                    <option>RAV4</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Year</label>
                <select name="year" class="w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 text-sm dark:bg-gray-700 dark:text-gray-200">
                    <option>2030</option>
                    <option>2029</option>
                    <option>2028</option>
                </select>
            </div>
            <div class="sm:col-span-3">
                <button type="submit" class="w-full bg-indigo-600 text-white py-2 rounded hover:bg-indigo-700 transition font-medium">Search Parts</button>
            </div>
        </form>
    </div>

    <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4">Compatible Parts</h2>
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        @foreach (['Brake Pads', 'Oil Filter', 'Air Filter', 'Spark Plugs'] as $part)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 text-center">
                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $part }}</span>
            </div>
        @endforeach
    </div>
</div>
@endsection