@extends('layouts.admin')

@section('title', 'Product Details')
@section('page_title', $product->name)

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-semibold text-gray-800">Product Details</h3>
            <div class="flex space-x-2">
                <a href="{{ route('admin.products.edit', $product) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
                <a href="{{ route('admin.products.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg transition">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Product Image -->
                @if($product->image)
                <div>
                    <h4 class="text-sm font-semibold text-gray-700 mb-3">Product Image</h4>
                    <img src="{{ asset('storage/' . $product->image) }}" 
                         class="w-full h-48 object-cover rounded-lg" alt="{{ $product->name }}">
                </div>
                @endif

                <!-- Product Info -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-600">Name</label>
                        <p class="text-gray-900">{{ $product->name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-600">Category</label>
                        <p class="text-gray-900">{{ $product->category->name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-600">Price</label>
                        <p class="text-gray-900 font-bold">${{ number_format($product->price, 2) }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-600">Stock</label>
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold {{ $product->stock > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $product->stock }} units
                        </span>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-600">Description</label>
                        <p class="text-gray-900">{{ $product->description }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-600">Created At</label>
                        <p class="text-gray-900">{{ $product->created_at->format('M d, Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection