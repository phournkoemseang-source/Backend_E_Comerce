@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@section('content')
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
        <!-- Total Products -->
        <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Total Products</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $stats['total_products'] }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <i class="fas fa-box text-blue-600 text-2xl"></i>
                </div>
            </div>
            <a href="{{ route('admin.products.index') }}" class="text-blue-600 text-sm mt-4 inline-block hover:underline">
                View Products →
            </a>
        </div>

        <!-- Total Categories -->
        <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Total Categories</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $stats['total_categories'] }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <i class="fas fa-list text-green-600 text-2xl"></i>
                </div>
            </div>
            <a href="{{ route('admin.categories.index') }}" class="text-green-600 text-sm mt-4 inline-block hover:underline">
                View Categories →
            </a>
        </div>

        <!-- Total Orders -->
        <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Total Orders</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $stats['total_orders'] }}</p>
                </div>
                <div class="bg-purple-100 rounded-full p-3">
                    <i class="fas fa-shopping-cart text-purple-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Users -->
        <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Total Users</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $stats['total_users'] }}</p>
                </div>
                <div class="bg-yellow-100 rounded-full p-3">
                    <i class="fas fa-users text-yellow-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Total Revenue</p>
                    <p class="text-3xl font-bold text-green-600">${{ number_format($stats['total_revenue'], 2) }}</p>
                </div>
                <div class="bg-red-100 rounded-full p-3">
                    <i class="fas fa-dollar-sign text-red-600 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Products -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-box mr-2 text-blue-600"></i>Recent Products
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Name</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Price</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recent_products as $product)
                            <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                                <td class="px-6 py-3 text-sm text-gray-800">{{ $product->name }}</td>
                                <td class="px-6 py-3 text-sm text-gray-600">${{ number_format($product->price, 2) }}</td>
                                <td class="px-6 py-3 text-sm">
                                    <span class="inline-block bg-blue-100 text-blue-800 text-xs px-3 py-1 rounded-full">
                                        {{ $product->stock }} units
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-center text-gray-600">No products yet</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-shopping-cart mr-2 text-purple-600"></i>Recent Orders
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Order ID</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Total</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recent_orders as $order)
                            <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                                <td class="px-6 py-3 text-sm font-semibold text-gray-800">#{{ $order->id }}</td>
                                <td class="px-6 py-3 text-sm text-gray-600">${{ number_format($order->total_amount, 2) }}</td>
                                <td class="px-6 py-3 text-sm">
                                    <span class="inline-block bg-yellow-100 text-yellow-800 text-xs px-3 py-1 rounded-full">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-center text-gray-600">No orders yet</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
