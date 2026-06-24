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
                                    <form method="POST" action="{{ route('admin.products.destroy', $product) }}" class="inline delete-form-{{ $product->id }}">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                    <button type="button" class="delete-product-btn text-red-600 hover:text-red-800 text-sm font-semibold" data-product-name="{{ $product->name }}" data-product-id="{{ $product->id }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('.delete-product-btn');
    const modal = document.getElementById('delete-modal');
    const modalProductName = document.getElementById('modal-product-name');
    const confirmButton = document.getElementById('confirm-delete-btn');
    const cancelButton = document.getElementById('cancel-delete-btn');
    let deleteForm = null;

    // Create modal if not exists
    if (!modal) {
        const modalHtml = `
            <div id="delete-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
                <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 transform transition-all">
                    <div class="p-6">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-800">Delete Product</h3>
                        </div>
                        <p class="text-gray-600 mb-6">
                            Are you sure you want to delete the product <span id="modal-product-name" class="font-semibold text-gray-800"></span>? This action cannot be undone.
                        </p>
                        <div class="flex justify-end space-x-3">
                            <button type="button" id="cancel-delete-btn" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg transition font-medium">
                                Cancel
                            </button>
                            <button type="button" id="confirm-delete-btn" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition font-medium">
                                Delete Product
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', modalHtml);
    }

    const modalEl = document.getElementById('delete-modal');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.dataset.productId;
            deleteForm = document.querySelector('.delete-form-' + productId);
            const productName = this.dataset.productName;
            document.getElementById('modal-product-name').textContent = productName;
            modalEl.style.display = 'flex';
        });
    });

    document.getElementById('confirm-delete-btn').addEventListener('click', function() {
        if (deleteForm) {
            deleteForm.submit();
        }
    });

    document.getElementById('cancel-delete-btn').addEventListener('click', function() {
        modalEl.style.display = 'none';
        deleteForm = null;
    });

    modalEl.addEventListener('click', function(e) {
        if (e.target === modalEl) {
            modalEl.style.display = 'none';
            deleteForm = null;
        }
    });
});
</script>
@endpush

<!-- Delete Confirmation Modal -->
<div id="delete-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 transform transition-all">
        <div class="p-6">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mr-4">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">Delete Product</h3>
            </div>
            <p class="text-gray-600 mb-6">
                Are you sure you want to delete the product <span id="modal-product-name" class="font-semibold text-gray-800"></span>? This action cannot be undone.
            </p>
            <div class="flex justify-end space-x-3">
                <button type="button" id="cancel-delete-btn" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg transition font-medium">
                    Cancel
                </button>
                <button type="button" id="confirm-delete-btn" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition font-medium">
                    Delete Product
                </button>
            </div>
        </div>
    </div>
</div>