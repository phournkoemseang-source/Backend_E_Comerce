@extends('layouts.admin')

@section('title', 'Category Products')
@section('page_title', 'Products in ' . $category->name)

@section('content')
    <div class="mb-6 flex justify-between items-center">
        <div>
            <p class="text-gray-600">Category: <span class="text-lg font-semibold text-gray-800">{{ $category->name }}</span></p>
            <p class="text-sm text-gray-500 mt-1">
                <i class="fas fa-link mr-1"></i>Slug: <code class="bg-gray-100 px-2 py-1 rounded">{{ $category->slug }}</code>
            </p>
        </div>
        <a href="{{ route('admin.categories.index') }}" class="text-blue-600 hover:text-blue-800 font-semibold">
            <i class="fas fa-arrow-left mr-2"></i>Back to Categories
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if ($products->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-100 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Name</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Price</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Stock</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Created</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($products as $product)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 text-sm font-semibold text-gray-800">{{ $product->name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">${{ number_format($product->price, 2) }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold {{ $product->stock > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $product->stock }} units
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $product->created_at->format('M d, Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="p-6 border-t border-gray-200">
                {{ $products->links() }}
            </div>
        @else
            <div class="px-6 py-8 text-center text-gray-600">
                <i class="fas fa-inbox text-4xl mb-2 text-gray-400"></i>
                <p class="mt-2">No products in this category</p>
            </div>
        @endif
    </div>
@endsection
