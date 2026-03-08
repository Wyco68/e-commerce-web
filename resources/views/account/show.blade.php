@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">My Account</h1>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
        <div class="space-y-4">
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Name</dt>
                <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ $user->name }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</dt>
                <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ $user->email }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Phone Number</dt>
                <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ $user->phone_num ?? 'Not set' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Address</dt>
                <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ $user->address ?? 'Not set' }}</dd>
            </div>
        </div>
    </div>
</div>
@endsection
