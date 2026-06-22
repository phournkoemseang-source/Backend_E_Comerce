@extends('layouts.admin')

@section('title', 'Products')
@section('page_title', 'Products Management')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-lg font-semibold text-gray-800">All Products</h3>
        <a href="{{ route('admin.products.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition flex items-center">
            <i class="fas fa-plus mr-2"></i>Add New Product
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-100 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Image</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Name</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Category</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Price</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Stock</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Created</th>
                        <th class="px-6 py-3 text-center text-sm font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($products as $product)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                @if($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}" 
                                         class="w-16 h-16 object-cover rounded" alt="{{ $product->name }}">
                                @else
                                    <div class="w-16 h-16 bg-gray-200 rounded flex items-center justify-center">
                                        <i class="fas fa-image text-gray-400"></i>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-800">{{ $product->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded">{{ $product->category->name }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">${{ number_format($product->price, 2) }}</td>
                            <td class="px-6 py-4 text-sm">
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold {{ $product->stock > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $product->stock }} units
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $product->created_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center space-x-2">
                                    <a href="{{ route('admin.products.edit', $product) }}" class="text-blue-600 hover:text-blue-800 text-sm font-semibold">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.products.destroy', $product) }}" class="inline" onsubmit="return confirm('Are you sure?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-semibold">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-600">
                                <i class="fas fa-inbox text-4xl mb-2 text-gray-400"></i>
                                <p class="mt-2">No products found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $products->links() }}
    </div>
@endsection
